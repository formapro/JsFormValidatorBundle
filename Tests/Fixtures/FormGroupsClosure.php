<?php

namespace Fp\JsFormValidatorBundle\Tests\Fixtures;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class FormGroupsClosure
 *
 * @package Fp\JsFormValidatorBundle\Tests\Fixtures
 */
class FormGroupsClosure extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'text');
    }

    /**
     * @param OptionsResolverInterface $resolver
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Fp\JsFormValidatorBundle\Tests\Fixtures\Entity',
            'validation_groups' => function() {
                return array('test');
            }
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'fp_jsformvalidatorbundle_tests_fixtures_formgroupsclosure';
    }
}
