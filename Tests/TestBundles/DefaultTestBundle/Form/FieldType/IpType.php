<?php
namespace Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Form\FieldType;

use Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Form\DataTransformer\IpToPartsTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\ReversedTransformer;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class IpType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('ip1', 'text', array(
                'label' => false,
                'error_bubbling' => true,
                'constraints' => array(
                    new NotBlank(),
                    new Length(array(
                        'min' => 1,
                        'max' => 3
                    )),
                ),
            ))
            ->add('ip2', 'text', array(
                'label' => false,
                'error_bubbling' => true,
                'constraints' => array(
                    new NotBlank(),
                    new Length(array(
                        'min' => 1,
                        'max' => 3
                    )),
                ),
            ))
            ->add('ip3', 'text', array(
                'label' => false,
                'error_bubbling' => true,
                'constraints' => array(
                    new NotBlank(),
                    new Length(array(
                        'min' => 1,
                        'max' => 3
                    )),
                ),
            ))
            ->add('ip4', 'text', array(
                'label' => false,
                'error_bubbling' => true,
                'constraints' => array(
                    new NotBlank(),
                    new Length(array(
                        'min' => 1,
                        'max' => 3
                    )),
                ),
            ))
            ->addModelTransformer(
                new ReversedTransformer(
                    new IpToPartsTransformer()
                )
            );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                 'ssn1' => '',
                 'ssn2' => '',
                 'ssn3' => '',
            )
        );
    }

    public function getName()
    {
        return 'ssn';
    }
}
