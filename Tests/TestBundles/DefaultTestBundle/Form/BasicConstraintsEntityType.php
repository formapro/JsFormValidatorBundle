<?php

namespace Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class BasicConstraintsEntityType
 *
 * @package Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Form
 */
class BasicConstraintsEntityType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('blank')
            ->add('notBlank')
            ->add('email')
            ->add('url')
            ->add('regex')
            ->add('ip')
            ->add('time', 'text')
            ->add('date', 'text')
            ->add('datetime', 'text');
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Entity\BasicConstraintsEntity',
            'js_validation' => true
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'form';
    }
}
