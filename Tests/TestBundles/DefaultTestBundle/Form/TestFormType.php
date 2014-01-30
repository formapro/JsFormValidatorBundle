<?php

namespace Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class TestFormType
 *
 * @package Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Form
 */
class TestFormType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array(
                'constraints' => array(
                    new NotBlank(array(
                        'message' => 'form_no_groups_message'
                    )),
                    new NotBlank(array(
                        'message' => 'form_groups_array_message',
                        'groups' => array('groups_array')
                    )),
                    new NotBlank(array(
                        'message' => 'form_groups_callback_message',
                        'groups' => array('groups_callback')
                    ))
                )
            ))
            ->add('clear');
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Entity\TestEntity',
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
