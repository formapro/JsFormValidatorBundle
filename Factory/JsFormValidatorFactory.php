<?php
/**
 * Created by PhpStorm.
 * User: Yury Maltsev
 * Email: dev.ymalcev@gmail.com
 * Date: 10/24/13
 * Time: 3:14 PM
 */

namespace Fp\JsFormValidatorBundle\Factory;

use Fp\JsFormValidatorBundle\Model\JsFormElement;
use Fp\JsFormValidatorBundle\Model\JsValidationData;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceListInterface;
use Symfony\Component\Form\Extension\Core\DataTransformer\ArrayToPartsTransformer;
use Symfony\Component\Form\Form;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Mapping\GetterMetadata;
use Symfony\Component\Validator\Mapping\PropertyMetadata;
use Symfony\Component\Validator\Validator;

class JsFormValidatorFactory
{
    const TRANSFORMER_CHAIN_CLASS = 'Symfony\Component\Form\Extension\Core\DataTransformer\DataTransformerChain';

    /**
     * @var Validator
     */
    protected $validator;

    /**
     * @var Translator
     */
    protected $translator;

    /**
     * @var Router
     */
    protected $router;

    /**
     * @var array
     */
    protected $config = array();

    /**
     * @param Validator  $validator
     * @param Translator $translator
     * @param Router     $router
     * @param array      $config
     */
    public function __construct(Validator $validator, Translator $translator, Router $router, $config)
    {
        $this->validator  = $validator;
        $this->translator = $translator;
        $this->router     = $router;
        $this->config     = $config;
    }

    /**
     * @codeCoverageIgnore
     *
     * @param string $className
     *
     * @return \Symfony\Component\Validator\MetadataInterface
     */
    protected function getMetadataFor($className)
    {
        return $this->validator->getMetadataFactory()->getMetadataFor($className);
    }

    /**
     * Translage a single message
     * @codeCoverageIgnore
     *
     * @param string $message
     *
     * @return string
     */
    protected function translateMessage($message)
    {
        $config = $this->getConfig();

        return $this->translator->trans($message, array(), $config['translation_domain']);
    }

    /**
     * Generate an URL from the route
     * @codeCoverageIgnore
     *
     * @param string $route
     *
     * @return string
     */
    protected function generateUrl($route)
    {
        return $this->router->generate($route);
    }

    /**
     * @return mixed
     */
    public function getConfig()
    {
        return $this->config;
    }


    /**
     * @param \Fp\JsFormValidatorBundle\Model\JsFormElement $model
     *
     * @return string
     */
    public function generateInlineJs(JsFormElement $model)
    {
        return "<script type=\"text/javascript\">FpJsFormValidatorFactory.initNewModel(" . $model . ')</script>';
    }

    /**
     * @param Form         $form
     * @param mixed        $metadata
     * @param array|string $groups
     *
     * @return JsFormElement
     */
    public function createJsModel(Form $form, $metadata = null, $groups = array())
    {
        $model = new JsFormElement($this->getElementId($form), $form->getName());
        $model->setType($form->getConfig()->getType());
        $model->setConfig($this->getPreparedConfig());
        $model->setInvalidMessage($form->getConfig()->getOption('invalid_message'));
        $model->setTransformers($this->getTransformersList($form));
        $model->setCascadeValidation($form->getConfig()->getOption('cascade_validation'));
        $model->addValidationData($this->getElementValidationData($form, $groups));
        $model->addValidationData($this->getMappingValidationData($metadata, $groups));

        if ($this->hasMetadata($form)) {
            $metadata = $this->getEntityMetadata($form);
            $groups   = $this->getValidationGroups($form);
            $model->addValidationData($this->getMappingValidationData($metadata, $groups));
            $model->setDataClass(addcslashes($form->getConfig()->getDataClass(), '\\'));
        } elseif (!$metadata instanceof ClassMetadata) {
            $metadata = null;
        }
        $model->setChildren($this->processChildren($form, $metadata, $groups));

        // Return self id to add it as child to the parent model
        return $model;
    }

    /**
     * @param Form $form
     *
     * @return null|ClassMetadata|PropertyMetadata
     */
    protected function getEntityMetadata(Form $form)
    {
        if ($this->hasMetadata($form)) {
            return $this->getMetadataFor($form->getConfig()->getDataClass());
        } else {
            return null;
        }
    }

    /**
     * @param Form $form
     *
     * @return string
     */
    protected function getElementId(Form $form)
    {
        /** @var Form $parent */
        $parent = $form->getParent();
        if (null !== $parent) {
            return $this->getElementId($parent) . '_' . $form->getName();
        } else {
            return $form->getName();
        }
    }

    /**
     * @param Form $element
     * @param      $groups
     *
     * @return JsValidationData
     */
    protected function getElementValidationData(Form $element, $groups)
    {
        $data = new JsValidationData($groups, get_class($element));
        $data->setConstraints(
            $this->parseConstraints(
                (array)$element->getConfig()->getOption('constraints')
            )
        );

        return $data;
    }

    /**
     * @param null|array|ClassMetadata|PropertyMetadata $metadata
     * @param                                           $groups
     *
     * @return array|JsValidationData
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
     */
    protected function getMappingValidationData($metadata, $groups)
    {
        if (is_array($metadata)) {
            $result = array();
            foreach ($metadata as $singleData) {
                $result[] = $this->getMappingValidationData($singleData, $groups);
            }

            return $result;

        } elseif ($metadata instanceof ClassMetadata || $metadata instanceof PropertyMetadata) {
            /** @var ClassMetadata|PropertyMetadata $metadata */
            $data = new JsValidationData($groups, get_class($metadata));
            $data->setConstraints(
                $this->parseConstraints(
                    $metadata->getConstraints()
                )
            );
            if (!empty($metadata->getters)) {
                $data->setGetters($this->parseGetters($metadata->getters));
            }

            return $data;

        } else {
            return array();
        }
    }

    /**
     * @param null|Form          $form
     * @param null|ClassMetadata $metadata
     * @param array              $groups
     *
     * @return array
     */
    protected function processChildren($form, $metadata, $groups)
    {
        $result = array();
        // If this field has children - process them
        foreach ($form as $name => $child) {
            if ($this->isProcessableElement($child)) {
                $childMetadata = ($metadata instanceof ClassMetadata) && ($metadata->hasMemberMetadatas($name))
                    ? $metadata->getMemberMetadatas($name)
                    : null;
                $childModel    = $this->createJsModel($child, $childMetadata, $groups, false);

                $result[$name] = $childModel;
            }
        }

        return $result;
    }

    /**
     * @param Form $form
     *
     * @return array|string
     */
    protected function getValidationGroups(Form $form)
    {
        $groups = $form->getConfig()->getOption('validation_groups');

        // If groups is an array - return groups as is
        if (is_array($groups)) {
            return $groups;
            // If groups is a Closure - return the form class name to look for javascrip
        } elseif ($groups instanceof \Closure) {
            return get_class($form->getConfig()->getType()->getInnerType());
        } else {
            return array();
        }
    }

    /**
     * @param Form $form
     *
     * @return bool
     */
    protected function hasMetadata(Form $form)
    {
        return null !== $form->getConfig()->getDataClass();
    }

    /**
     * @param mixed $element
     *
     * @return bool
     */
    protected function isProcessableElement($element)
    {
        return ($element instanceof Form)
        && ('hidden' !== $element->getConfig()->getType()->getName());
    }

    /**
     * @param Form $data
     *
     * @return array
     */
    protected function getTransformersList(Form $data)
    {
        return $this->parseTransformers($data->getConfig()->getViewTransformers());
    }

    /**
     * @param array $transformers
     *
     * @return array
     */
    protected function parseTransformers(array $transformers)
    {
        $result = array();
        foreach ($transformers as $trans) {
            $item = array();

            $reflect = new \ReflectionClass($trans);
            $properties = $reflect->getProperties();
            foreach ($properties as $prop) {
                $item[$prop->getName()] = $this->getTransformerParam($trans, $prop->getName());
            }

            $item['name'] = get_class($trans);

            $result[] = $item;
        }

        return $result;
    }

    /**
     * @param DataTransformerInterface $transformer
     * @param string                   $paramName
     *
     * @return mixed
     */
    protected function getTransformerParam(DataTransformerInterface $transformer, $paramName)
    {
        $reflection = new \ReflectionProperty($transformer, $paramName);
        $reflection->setAccessible(true);
        $value = $reflection->getValue($transformer);
        $result = null;

        if ('transformers' === $paramName && is_array($value)) {
            $result = $this->parseTransformers($value);
        } elseif (is_scalar($value) || is_array($value)) {
            $result = $value;
        } elseif ($value instanceof ChoiceListInterface) {
            $result = $value->getChoices();
        }

        return $result;
    }

    /**
     * @param GetterMetadata[] $getters
     *
     * @return array
     */
    protected function parseGetters(array $getters)
    {
        $result = array();
        foreach ($getters as $name => $getter) {
            $result[$name] = array(
                'class'       => $getter->getClassName(),
                'name'        => $getter->getName(),
                'constraints' => $this->parseConstraints((array)$getter->getConstraints()),
            );
        }

        return $result;
    }

    /**
     * @param array $constraints
     *
     * @return array
     */
    protected function parseConstraints(array $constraints)
    {
        $result = array();
        foreach ($constraints as $constr) {
            // Translate messages if need and add to result
            $result[get_class($constr)][] = $this->translateConstraint($constr);
        }

        return $result;
    }

    /**
     * @param Constraint $constraint
     *
     * @return Constraint
     */
    protected function translateConstraint(Constraint $constraint)
    {
        foreach ($constraint as $propName => $propValue) {
            if (false !== strpos(strtolower($propName), 'message')) {
                $constraint->{$propName} = $this->translateMessage($propValue);
            }
        }

        return $constraint;
    }

    /**
     * @return array
     */
    protected function getPreparedConfig()
    {
        $result = array();
        if (!empty($this->config['routing'])) {
            foreach ($this->config['routing'] as $param => $value) {
                $result['routing'][$param] = $this->generateUrl($value);
            }
        }

        return $result;
    }

} 