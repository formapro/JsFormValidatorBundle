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
class EmtyChoiceType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'city',
            'choice',
            array(
                'empty_value' => 'Choose a City',
                'choices'     => array(
                    'london' => 'London',
                    'paris'  => 'Paris',
                    'berlin' => 'Berlin',
                ),
                'multiple'    => false,
                'expanded'    => false,
            )
        )
        ->add(
            'countries',
            'choice',
            array(
                'empty_value' => 'Choose countries',
                'choices'     => array(
                    'france' => 'France',
                    'spain'  => 'Spain',
                    'germany' => 'Germany',
                ),
                'multiple'    => true,
                'expanded'    => true,
            )
        )
        ->add(
            'continent',
            'choice',
            array(
                'empty_value' => 'Choose continent',
                'choices' => array(
                    'africa' => 'Africa',
                    'asia'   => 'Asia',
                    'europe' => 'Europe',
                ),
                'multiple' => false,
                'expanded' => true,
            )
        )
        ->add('submit', 'submit');
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Entity\EmptyChoiceEntity',
                'attr' => array(
                    'novalidate' => 'novalidate'
                )
            )
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'form_choice';
    }
}
