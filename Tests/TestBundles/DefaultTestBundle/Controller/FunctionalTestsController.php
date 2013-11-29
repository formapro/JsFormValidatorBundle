<?php

namespace Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Controller;

use Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Entity\BasicConstraintsEntity;
use Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Entity\TestEntity;
use Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Form\BasicConstraintsEntityType;
use Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Form\TestFormType;
use Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Form\TestSubFormType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Validator\Constraints\Blank;
use Symfony\Component\Validator\Constraints\False;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\True;

class FunctionalTestsController extends Controller
{
    /**
     * @Route("/fp_js_form_validator/javascript_unit_test/levels")
     * @Template("DefaultTestBundle:FunctionalTests:index.html.twig")
     */
    public function levelsAction()
    {
        $form = $this->createForm(new TestFormType(), new TestEntity());
        $form
            ->add('email', 'text', array(
                'constraints' => array(
                    new NotBlank(array('message' => 'controller_message'))
                )
            ));

        return array(
            'form' => $form,
        );
    }

    /**
     * @Route("/fp_js_form_validator/javascript_unit_test/translations")
     * @Template("DefaultTestBundle:FunctionalTests:index.html.twig")
     */
    public function translationAction()
    {
        $builder = $this->createFormBuilder(null, array());
        $builder
            ->add('name', 'text', array(
                'constraints' => array(
                    new NotBlank(array(
                        'message' => 'blank.translation'
                    ))
                )
            ));

        return array(
            'form' => $builder->getForm(),
        );
    }

    /**
     * @Route("/fp_js_form_validator/javascript_unit_test/groups_getters")
     * @Template("DefaultTestBundle:FunctionalTests:index.html.twig")
     */
    public function groupsAndGettersAction()
    {
        $parent = $this
            ->createForm(new TestFormType(), new TestEntity(), array('validation_groups' => array('parent')))
            ->add('name', new TestSubFormType(), array(
                'constraints' => array(
                    new NotBlank(array(
                        'message' => 'child_message'
                    ))
                ),
                'validation_groups' => array('child')
            ));

        return array(
            'form' => $parent,
        );
    }

    /**
     * @Route("/fp_js_form_validator/javascript_unit_test/basic_constraints/{isValid}")
     * @Template("DefaultTestBundle:FunctionalTests:index.html.twig")
     */
    public function basicConstraintsAction($isValid)
    {
        $data = array(
            array(
                'blank'    => null,
                'notBlank' => 'a',
                'email'    => 'example@google.com',
                'url'      => 'https://www.google.com',
                'regex'    => 'aaa',
                'ip'       => '125.125.125.0',
                'time'     => '12:15:32',
                'date'     => '2013-04-04',
                'datetime' => '2013-04-04 12:15:32',
            ),
            array(
                'blank'    => 'a',
                'notBlank' => null,
                'email'    => 'wrong_email',
                'url'      => 'wrong_url',
                'regex'    => 'bbb',
                'ip'       => '125.125.125',
                'time'     => '12/15/32',
                'date'     => '04/04/2013',
                'datetime' => '04/04/2013_12:15:32',
            )
        );

        $data = $isValid ? $data[0] : $data[1];
        $entity = new BasicConstraintsEntity();
        $entity->populate($data);
        $form = $this->createForm(new BasicConstraintsEntityType(), $entity);
        $form->handleRequest($this->getRequest());

        return array(
            'form' => $form,
            'isValid' => $isValid
        );
    }

    /**
         * @Route("/fp_js_form_validator/javascript_unit_test/transformers")
     * @Template("DefaultTestBundle:FunctionalTests:index.html.twig")
     */
    public function transformersAction()
    {
        $date = new \DateTime();
        $date->setDate(2009, 4, 7);
        $date->setTime(21, 15);

        $blank = new Blank(array('message' => '{{ value }}'));
        //$notBank = new NotBlank(array('message' => '{{ value }}'));
        $choices = array('m' => 'male', 'f' => 'female');

        $form = $this
            ->createFormBuilder(array(
                'date'                  => $date,
                'time'                  => $date,
                'datetime'              => $date,
                'checkbox'              => true,
                'ChoicesToValues'       => array('m', 'f'),
                'ChoiceToValue'         => 'f',
                'ChoicesToBooleanArray' => array('m', 'f'),
                'ChoiceToBooleanArray'  => 'f',
            ))
            ->add('date',     'date',     array('constraints' => array($blank)))
            ->add('time',     'time',     array('constraints' => array($blank)))
            ->add('datetime', 'datetime', array('constraints' => array($blank)))
            ->add('checkbox', 'checkbox', array('constraints' => array(new False(array('message' => '{{ value }}')))))
            ->add('radio',    'radio',    array('constraints' => array(new True(array('message' => '{{ value }}')))))
            ->add('ChoicesToValues',  'choice',   array(
                'multiple' => true,
                'choices' => $choices,
                'constraints' => array($blank)
            ))
            ->add('ChoiceToValue',  'choice',   array(
                'multiple' => false,
                'choices' => $choices,
                'constraints' => array($blank)
            ))
            ->add('ChoicesToBooleanArray',  'choice',   array(
                'expanded'    => true,
                'multiple'    => true,
                'choices'     => $choices,
                'constraints' => array($blank)
            ))
            ->add('ChoiceToBooleanArray',  'choice',   array(
                'expanded'    => true,
                'multiple'    => false,
                'choices'     => $choices,
                'constraints' => array($blank)
            ))
            ->getForm();

        $form->handleRequest($this->getRequest());

        return array(
            'form' => $form,
        );
    }
}
