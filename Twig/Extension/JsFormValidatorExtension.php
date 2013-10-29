<?php
/**
 * Created by PhpStorm.
 * User: Yury Maltsev
 * Email: dev.ymalcev@gmail.com
 * Date: 10/24/13
 * Time: 5:06 PM
 */
namespace Fp\JsFormValidatorBundle\Twig\Extension;

use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Component\Form\FormView;
use Symfony\Component\Validator\Validator;
use Doctrine\Common\Collections\ArrayCollection;
use Fp\JsFormValidatorBundle\Model\JsFormModel;
use Symfony\Component\Form\Form;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Mapping\GetterMetadata;
use Symfony\Component\Validator\Mapping\PropertyMetadata;

class JsFormValidatorExtension extends \Twig_Extension
{
    /**
     * @var string
     */
    protected $cacheDir;
    /**
     * @var Validator
     */
    protected $validator;
    /**
     * @var Translator
     */
    protected $translator;

    /**
     * @param $dir
     * @param Validator $validator
     * @param Translator $translator
     */
    public function __construct($dir, Validator $validator, Translator $translator)
    {
        $this->cacheDir   = $dir;
        $this->validator  = $validator;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            'fp_jsfv' => new \Twig_Function_Method($this, 'getJsValidator', ['is_safe' => ['html']]),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            'removeslashes' => new \Twig_Filter_Method($this, 'removeSlashes'),
        );
    }

    public function getJsValidator(FormView $formView)
    {
        $model = $this->getJsFormModel($formView);
        $json = json_encode($model);

        return '
            <script type="text/javascript">
                //FpJsFormValidator.forms[\''.$model->id.'\'] = JSON.parse(\''.$json.'\')
                var form = document.getElementById("'.$model->id.'");
                if (form.tagName.toLowerCase() !== "form") {
                    form = form.getParentByTagName("form");
                }
                form.addEventListener("submit", function(event){
                    event.preventDefault();
                    var result = this.fpValidate();
                    if (false === result) {
                        event.preventDefault();
                    }
                    return result;
                });
            </script>';
    }

    public function removeSlashes($string)
    {
        return preg_replace('/[\/\\\]+/', '', $string);
    }

    /**
     * @param FormView $form
     *
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
     * @return JsFormModel
     */
    private function getJsFormModel(FormView $form)
    {
        $opts = new ArrayCollection($form->vars);
        $formName = $opts->get('name');
        $groups = (array) $opts->get('validation_groups');
        $elements = $form->children;
        $dataClass = $opts->get('data_class');

        $model = new JsFormModel($formName, $groups);

        /** @var ClassMetadata $entityMetadata */
        $entityMetadata = $dataClass
            ? $this->validator->getMetadataFactory()->getMetadataFor($dataClass)
            : null;

        foreach ($elements as $name => $elem) {
            if (!$elem instanceof FormView) {
                continue;
            }

            // Add constraints from formType
            $model->addField($elem, $this->translator);

            // Add constraints from entity
            if ($entityMetadata && $entityMetadata->hasMemberMetadatas($name)) {
                $metadata = (array)$entityMetadata->getMemberMetadatas($name);
                /** @var $propertyData PropertyMetadata */
                foreach ($metadata as $propertyData) {
                    $model->addField($propertyData, $this->translator);
                }
            }
        }

        // Add method constraints
        if (null !== $entityMetadata) {
            /** @var $metaData GetterMetadata */
            foreach ($entityMetadata->getters as $method) {
                $model->addMethod($method, $this->translator);
            }
        }

        return $model;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'FpJsFormValidator';
    }
}
