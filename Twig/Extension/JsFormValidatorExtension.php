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
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Mapping\GetterMetadata;
use Symfony\Component\Validator\Mapping\PropertyMetadata;
use Doctrine\Common\Collections\ArrayCollection;
use Fp\JsFormValidatorBundle\Factory\JsFormValidatorFactory;

class JsFormValidatorExtension extends \Twig_Extension
{
    /**
     * @var Validator
     */
    protected $validator;
    /**
     * @var Translator
     */
    protected $translator;
    /**
     * @var string
     */
    protected $domain;

    /**
     * @param Validator $validator
     * @param Translator $translator
     * @param string $domain
     */
    public function __construct(Validator $validator, Translator $translator, $domain)
    {
        $this->validator  = $validator;
        $this->translator = $translator;
        $this->domain     = $domain;
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

    public function getJsValidator(FormView $formView, $config = [])
    {
        $opts = new ArrayCollection($formView->vars);

        // Create a factory
        $factory = new JsFormValidatorFactory(
            $opts->get('name'),
            (array) $opts->get('validation_groups'),
            $config,
            $this->translator,
            $this->domain
        );

        // Get metadata with constraints for entity (if has)
        $dataClass = $opts->get('data_class');
        /** @var ClassMetadata $entityMetadata */
        $entityMetadata = $dataClass
            ? $this->validator->getMetadataFactory()->getMetadataFor($dataClass)
            : null;

        // Iterate form elements and fill out the factory
        $elements = $formView->children;
        foreach ($elements as $name => $elem) {
            // Skip not FormView types
            if (!$elem instanceof FormView) continue;

            // Add constraints from the form field data
            $factory->addField($elem);

            // Add constraints from the entity metadata
            if ($entityMetadata && $entityMetadata->hasMemberMetadatas($name)) {
                $metadata = (array)$entityMetadata->getMemberMetadatas($name);
                /** @var $propertyData PropertyMetadata */
                foreach ($metadata as $propertyData) {
                    $factory->addField($propertyData);
                }
            }
        }

        // Add method constraints
        if (null !== $entityMetadata) {
            /** @var $metaData GetterMetadata */
            foreach ($entityMetadata->getters as $method) {
                $factory->addGetter($method);
            }
        }

        // Call factory to generate javascript that will create necessary validator objects
        return $factory->generateJavascript();
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
