<?php

declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class TestForm extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $testedChoices = [
            'Not null' => 'a',
            'True' => 'b',
            'False' => 'c',
            'Null' => 'd',
        ];

        $builder
            ->add('notBlank', TextType::class, [
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please fill field']),
                ],
            ])
            ->add('blank', TextType::class, [
                'constraints' => [
                    new Constraints\Blank(['message' => 'Please do not fill field']),
                ],
            ])
            ->add('choice', TextType::class, [
                'constraints' => [
                    new Constraints\Choice(['choices' => ['1', '2', '3'], 'message' => 'Please fill field correct value (1,2,3)']),
                ],
            ])
            ->add('date', TextType::class, [
                'constraints' => [
                    new Constraints\Date(['message' => 'Please fill valid date']),
                ],
            ])
            ->add('datetime', TextType::class, [
                'constraints' => [
                    new Constraints\DateTime(['message' => 'Please fill valid date time']),
                ],
            ])
            ->add('email', TextType::class, [
                'constraints' => [
                    new Constraints\Email(['message' => 'Please fill valid email']),
                ],
            ])
            ->add('equalTo', TextType::class, [
                'constraints' => [
                    new Constraints\EqualTo(['value' => 'abc', 'message' => 'Please fill correct value (20)']),
                ],
            ])
            ->add('greaterThan', TextType::class, [
                'constraints' => [
                    new Constraints\GreaterThan(['value' => '20', 'message' => 'Please fill greater than 20 value']),
                ],
            ])
            ->add('greaterThanOrEqual', TextType::class, [
                'constraints' => [
                    new Constraints\GreaterThanOrEqual(['value' => '20', 'message' => 'Please fill greater than or equal 20 value']),
                ],
            ])
            ->add('ip', TextType::class, [
                'constraints' => [
                    new Constraints\Ip(['message' => 'Please fill valid IP']),
                ],
            ])
            ->add('isFalse', CheckboxType::class, [
                'value' => true,
                'constraints' => [
                    new Constraints\IsFalse(['message' => 'Please choice false']),
                ],
            ])
            ->add('isTrue', CheckboxType::class, [
                'value' => true,
                'constraints' => [
                    new Constraints\IsTrue(['message' => 'Please choice true']),
                ],
            ])
            ->add('lessThan', TextType::class, [
                'constraints' => [
                    new Constraints\LessThan(['value' => '20', 'message' => 'Please fill least than 20 value']),
                ],
            ])
            ->add('lessThanOrEqual', TextType::class, [
                'constraints' => [
                    new Constraints\LessThanOrEqual(['value' => '20', 'message' => 'Please fill least than or equal 20 value']),
                ],
            ])
            ->add('notEqualTo', TextType::class, [
                'constraints' => [
                    new Constraints\NotEqualTo(['value' => 'abc', 'message' => 'Please fill correct value (not abc)']),
                ],
            ])
            ->add('range', TextType::class, [
                'constraints' => [
                    new Constraints\Range([
                        'min' => 120,
                        'max' => 180,
                        'minMessage' => 'You must be at least {{ limit }}',
                        'maxMessage' => 'You cannot be taller than {{ limit }}',
                    ]),
                ],
            ])
            ->add('url', TextType::class, [
                'constraints' => [
                    new Constraints\Url(['message' => 'Please fill valid url']),
                ],
            ])

            ->add('save', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'attr' => ['novalidate' => 'novalidate'],
        ]);
    }
}
