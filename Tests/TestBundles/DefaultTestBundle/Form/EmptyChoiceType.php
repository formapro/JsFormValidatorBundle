<?php

namespace Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class TestFormType
 *
 * @package Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Form
 */
class EmptyChoiceType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'city',
            ChoiceType::class,
            array(
                'placeholder' => 'Choose a City',
                'choices'     => array(
                    'London' => 'london',
                    'Paris'  => 'paris',
                    'Berlin' => 'berlin',
                ),
                'multiple'    => false,
                'expanded'    => false,
            )
        )
        ->add(
            'countries',
            ChoiceType::class,
            array(
                'placeholder' => 'Choose countries',
                'choices'     => array(
                    'France' => 'france',
                    'Spain'  => 'spain',
                    'Germany' => 'germany',
                ),
                'multiple'    => true,
                'expanded'    => true,
            )
        )
        ->add(
            'continent',
            ChoiceType::class,
            array(
                'placeholder' => 'Choose continent',
                'choices' => array(
                    'Africa' => 'africa',
                    'Asia'   => 'asia',
                    'Europe' => 'europe',
                ),
                'multiple' => false,
                'expanded' => true,
            )
        )
        ->add('submit', SubmitType::class);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
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
}
