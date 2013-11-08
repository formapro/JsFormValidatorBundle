<?php

namespace Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Form;

use Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Entity\User;
use Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Form\FieldType\IpType;
use Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Form\FieldType\SsnType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('email', 'email')
            ->add('password', 'repeated', array(
                'type'            => 'password',
                'invalid_message' => 'The password fields must match.',
                'required'        => true,
                'first_options'   => array('label' => 'Password'),
                'second_options'  => array('label' => 'Repeat Password'),
            ))
            ->add('age', 'integer')
            ->add('gender', 'choice', array(
                'attr'     => array(
                    'class' => 'radio-list'
                ),
                'choices'  => User::getGendersList(),
                'data'     => 'm',
                'expanded' => true,
                'multiple' => false,
            ))
            ->add('languages', 'language', array(
                'multiple' => true
            ))
            ->add('married', 'checkbox')
            ->add('os', 'choice', array(
                'attr'     => array(
                    'class' => 'checkbox-list'
                ),
                'choices'  => User::getOsList(),
                'data'     => array('w'),
                'expanded' => true,
                'multiple' => true,
            ))
            ->add('site', 'url')
            ->add('ip', new IpType(), array(
                'attr' => array(
                    'class' => 'ip-input'
                )
            ))
            ->add('avatar', 'file')
            ->add('addresses', 'collection', array(
                'type' => new AddressType(),
                'allow_add' => true,
                'attr' => array(
                    'class' => 'sub-form-list'
                )
            ))
            ->add('books', 'collection', array(
                'type' => new BookType(),
                'allow_add' => true,
                'attr' => array(
                    'class' => 'sub-form-list'
                )
            ))
            ->add('role', 'entity', array(
                'class' => 'Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Entity\Role',
                'data' => 1
            ))
            ->add('save', 'submit')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Entity\User'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'fp_jsformvalidatorbundle_tests_testbundles_defaulttestbundle_user';
    }
}
