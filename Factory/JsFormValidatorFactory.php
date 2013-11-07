<?php
/**
 * Created by PhpStorm.
 * User: Yury Maltsev
 * Email: dev.ymalcev@gmail.com
 * Date: 10/24/13
 * Time: 3:14 PM
 */

namespace Fp\JsFormValidatorBundle\Factory;

use Doctrine\Common\Collections\ArrayCollection;
use Fp\JsFormValidatorBundle\Model\JsFormTypeModel;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormView;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Mapping\GetterMetadata;
use Symfony\Component\Validator\Mapping\PropertyMetadata;
use Symfony\Component\Validator\Validator;

class JsFormValidatorFactory {
    /**
     * @var array
     */
    protected $defaultGroups = ['Default'];

    /**
     * @var JsFormTypeModel[]
     */
    protected $elements = [];

    /**
     * @var string
     */
    protected $parentFormId;

    /**
     * @var Validator
     */
    protected $validator;

    /**
     * @var Translator
     */
    protected $translator;

    /**
     * @var array
     */
    protected $config;

    /**
     * @param Validator $validator
     * @param null|Translator $translator
     * @param array $config
     *
     */
    public function __construct(Validator $validator, Translator $translator, $config)
    {
        $this->validator  = $validator;
        $this->translator = $translator;
        $this->config     = $config;
    }

    public function processForm(Form $form)
    {
        $this->elements     = [];
        $formView           = $form->createView();
        $this->parentFormId = $formView->vars['id'];

        $this->processElementRecursively($formView, $form);
    }

    /**
     * @return string
     */
    public function generateJavascript()
    {
        $resultJs = '';
        foreach ($this->elements as $model) {
            $resultJs .= $model->createJsObject() . "\n";
        }

        return "<script type='text/javascript'>".$resultJs."</script>";
    }

    /**
     * @param FormView $view
     * @param Form $form
     * @param null|PropertyMetadata $metadata
     * @param array $groups
     *
     * @return string
     */
    public function processElementRecursively(FormView $view, Form $form = null, $metadata = null, $groups = []) {
        $model = new JsFormTypeModel($this->parentFormId);
        $opts  = new ArrayCollection($view->vars);

        // If empty metadata and this is parent form:
        $dataClass    = $opts->get('data_class');
        $isParentForm = false;
        if (null === $metadata && null !== $dataClass) {
            $isParentForm = true;
            // Get the new metadata
            /** @var ClassMetadata $metadata */
            $metadata = $this->validator->getMetadataFactory()->getMetadataFor($dataClass);
            // Redefine groups
            $groups = array_merge((array)$opts->get('validation_groups'), $this->defaultGroups);
            // Looking for getters in the parent metadata and set them to the model
            $model->getters = $this->parseGetters($metadata->getters, $groups);
        }

        $model->id           = $opts->get('id');
        $model->name         = $opts->get('name');
        $model->fullName     = $opts->get('full_name');
        // Looking for data-transformers in the form if the form element exists
        if (null !== $form) {
            $model->transformers = $this->getTransformersList($form->getConfig()->getViewTransformers());
        }
        // Get constraints from view
        $constraints = (array) $opts->get('constraints');
        // And get constraints form metadata
        if (null !== $metadata) {
            $constraints = array_merge($constraints, $metadata->getConstraints());
        }
        // Set the constraints to the model
        $model->constraints = $this->parseConstraints($constraints, $groups);
        // If this field has children - process them
        if (!empty($view->children)) {
            foreach ($view->children as $name => $child) {
                $childView = $child;
                $childForm = $form->has($name) ? $form->get($name) : null;
                if ($childForm instanceof Form && 'hidden' !== $childForm->getConfig()->getType()->getName()) {
                    $childMetadata = null;
                    if ($metadata instanceof ClassMetadata && $metadata->hasMemberMetadatas($name)) {
                        $childMetadata = $metadata->getMemberMetadatas($name);
                        /** @var PropertyMetadata $childMetadata */
                        $childMetadata = is_array($childMetadata) ? $childMetadata[0] : $childMetadata;
                    }
                    $model->children[] = $this->processElementRecursively($childView, $childForm, $childMetadata, $groups);
                }
            }
        }
        // If this emelemt has some validations or it is parent element - add it to the container
        if ($isParentForm || !empty($model->constraints) || !empty($model->getters)) {
            $this->elements[] = $model;
        }
        // Return self id to save it in the parent's "children" container
        return $model->id;
    }

    /**
     * @param array $transformers
     *
     * @return array
     */
    protected function getTransformersList($transformers)
    {
        $result = [];

        foreach ($transformers as $trans) {
            $name = get_class($trans);
            $chainClass = 'Symfony\Component\Form\Extension\Core\DataTransformer\DataTransformerChain';

            if ($chainClass == $name) {
                $reflection = new \ReflectionProperty($chainClass, 'transformers');
                $reflection->setAccessible(true);
                $result[] = [
                    'name' => str_replace('\\', '', $chainClass),
                    'transformers' => $this->getTransformersList($reflection->getValue($trans))
                ];
            } else {
                $result[] = [
                    'name' => str_replace('\\', '', $name)
                ];
            }
        }

        return $result;
    }

    /**
     * @param GetterMetadata[] $getters
     * @param array $groups
     *
     * @return array
     */
    protected function parseGetters(array $getters, array $groups)
    {
        $result = [];
        foreach ($getters as $getter) {
            $constraints = (array) $getter->getConstraints();
            /** @var $constr Constraint */
            foreach ($constraints as $constr) {
                // Check if constraint is in the allowed groups
                $mutual = array_intersect($groups, $constr->groups);
                if (!empty($mutual)) {
                    // Save constraints to the 'getters' bag by schema: ClassName => MethodName => constraints
                    $entityName = str_replace('\\', '', $getter->getClassName());
                    $constrName = str_replace('\\', '', get_class($constr));
                    $result[$entityName][$getter->getName()][$constrName][] = $this->translateMessages($constr);
                }
            }
        }

        return $result;
    }

    /**
     * @param array $constraints
     * @param array $groups
     *
     * @return array
     */
    protected function parseConstraints(array $constraints, array $groups)
    {
        $result = [];
        foreach ($constraints as $constr) {
            // Check if constraint is in the allowed groups
            $mutual = array_intersect($groups, $constr->groups);
            if (!empty($mutual)) {
                // Simplify a full class name to a string without slashes
                $constrName = str_replace('\\', '', get_class($constr));

                // Translate messages if need and add to storage
                $result[$constrName][] = $this->translateMessages($constr);
            }
        }

        return $result;
    }

    /**
     * Translage messages in a constarint
     *
     * @param Constraint $constraint
     *
     * @return Constraint
     */
    protected function translateMessages(Constraint $constraint) {
        if (null !== $this->translator) {
            // Translate each string that contains the substring 'message'
            foreach ($constraint as $propName => $propValue) {
                if (false !== strpos(strtolower($propName), 'message')) {
                    $constraint->{$propName} = $this->translator->trans($propValue, [], $this->config['translation_domain']);
                }
            }
        }

        return $constraint;
    }
} 