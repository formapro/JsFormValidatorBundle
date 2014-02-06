<?php

namespace Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class TestFormType
 *
 * @package Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Form
 */
class CustomizationType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('disabled')
            ->add('showErrors')
            ->add('callbackGroups')
            ->add('email')
            ->add('submit', 'submit');
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class'        => 'Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Entity\CustomizationEntity',
                'validation_groups' => function () {
                        return array('groups_callback');
                    }
            )
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'form';
    }
}
