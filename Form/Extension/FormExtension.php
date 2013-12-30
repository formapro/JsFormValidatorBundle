<?php
namespace Fp\JsFormValidatorBundle\Form\Extension;

use Fp\JsFormValidatorBundle\Form\EventSubscriber\FormSubscriber;
use Fp\JsFormValidatorBundle\Factory\JsFormValidatorFactory;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class FormExtension
 *
 * @package Fp\JsFormValidatorBundle\Form\Extension
 */
class FormExtension extends AbstractTypeExtension
{
    /**
     * @var JsFormValidatorFactory
     */
    protected $factory;

    /**
     * @param JsFormValidatorFactory $factory
     */
    public function __construct(JsFormValidatorFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!$options['js_validation']) {
            return;
        }

        $builder->addEventSubscriber(new FormSubscriber($this->factory));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array('js_validation' => false));
    }

    /**
     * Returns the name of the type being extended.
     *
     * @return string The name of the type being extended
     */
    public function getExtendedType()
    {
        return 'form';
    }
}