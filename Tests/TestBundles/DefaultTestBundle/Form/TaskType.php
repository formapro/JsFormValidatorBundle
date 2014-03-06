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
class TaskType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'tags',
            'collection',
            array(
                'type'      => new TagType(),
                'allow_add' => true,
            )
        )
        ->add(
            'comments',
            'collection',
            array(
                'type'      => new CommentType(),
                'allow_add' => true,
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
                'cascade_validation' => true,
                'data_class'         => 'Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Entity\TaskEntity',
            )
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'form_task';
    }
}
