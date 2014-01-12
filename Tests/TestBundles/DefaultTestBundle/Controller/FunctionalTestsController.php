<?php

namespace Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Controller;

use Composer\Factory;
use Fp\JsFormValidatorBundle\Factory\JsFormValidatorFactory;
use Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Entity\BasicConstraintsEntity;
use Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Entity\TestEntity;
use Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Form\BasicConstraintsEntityType;
use Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Form\TestFormType;
use Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Form\TestSubFormType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Blank;
use Symfony\Component\Validator\Constraints\False;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\True;
use Symfony\Component\Validator\Constraints\Type;

/**
 * Class FunctionalTestsController
 *
 * @package Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Controller
 */
class FunctionalTestsController extends Controller
{
    /**
     * Check forms and subforms
     *
     * @return \Symfony\Component\HttpFoundation\Response
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

        return $this->render(
            'DefaultTestBundle:FunctionalTests:index.html.twig',
            array('form' => $form->createView())
        );
    }

    /**
     * Check translation service
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @param string                                    $type
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function translationAction(Request $request, $type)
    {
        if ($type == 2) {
            /** @var JsFormValidatorFactory $factory */
            $factory = $this->get('fp_js_form_validator.factory');

            $config = $factory->getConfig();
            $config['translation_domain'] = 'test';

            $reflection = new \ReflectionProperty($factory, 'config');
            $reflection->setAccessible(true);
            $reflection->setValue($factory, $config);
        }

        $builder = $this->createFormBuilder(null, array('js_validation' => ($type > 0)));
        $builder
            ->add('name', 'text', array(
                'constraints' => array(
                    new NotBlank(array(
                        'message' => 'blank.translation'
                    ))
                )
            ));

        $form = $builder->getForm();
        if ($request->isMethod('post')) {
            $form->submit($request);
        }

        return $this->render(
            'DefaultTestBundle:FunctionalTests:index.html.twig',
            array('form' => $form->createView())
        );
    }

    /**
     * Check groups as array and as callback.
     * Also check initializing of getters
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param string                                    $type
     * @param string                                    $js
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function groupsGettersCascadeAction(Request $request, $type, $js)
    {
        switch ($type) {
            case 'array':
                $form = $this->getSimpleForm(array('groups_array'), (bool) $js);
                break;
            case 'callback':
                $form = $this->getSimpleForm(function() {return array('groups_callback');}, (bool) $js);
                break;
            case 'nested':
                $form = $this->getNestedForm(true, array('groups_child'), (bool) $js);
                break;
            case 'nested_no_groups':
                $form = $this->getNestedForm(true, array(), (bool) $js);
                break;
            case 'nested_no_cascade':
                $form = $this->getNestedForm(false, array(), (bool) $js);
                break;
            default:
                $form = $this->getSimpleForm(array(), (bool) $js);
                break;
        }

        if ($request->isMethod('post')) {
            $form->submit($request);
        }

        return $this->render(
            'DefaultTestBundle:FunctionalTests:index.html.twig',
            array('form' => $form->createView())
        );
    }

    /**
     * @param Form $form
     *
     * @return array
     */
    protected function getGroups($form)
    {
        $result = array();
        $result['groups'] = $form->getConfig()->getOption('validation_groups');
        $result['children'] = array();
        /** @var Form $element */
        foreach ($form->all() as $name => $element) {
            $result['children'][$name] = $this->getGroups($element);
        }

        return $result;
    }

    protected function getSimpleForm($groups, $js)
    {
        return $this
            ->createForm(
                new TestFormType(),
                new TestEntity(),
                array(
                    'validation_groups' => $groups,
                    'js_validation'     => $js
                )
            );
    }

    protected function getNestedForm($cascade, $childGroups, $js)
    {
        return $this
            ->createForm(
                new TestFormType(),
                new TestEntity(),
                array(
                    'validation_groups' => array('groups_array'),
                    'cascade_validation' => $cascade,
                    'js_validation'     => $js,
                )
            )
            ->add('email', new TestSubFormType(), array(
                'error_bubbling' => false,
                'constraints' => array(
                    new Type(array(
                        'type' => 'integer',
                        'message' => 'child_groups_array_message',
                        'groups' => array('groups_array'),
                    )),
                    new Type(array(
                        'type' => 'integer',
                        'message' => 'child_groups_child_message',
                        'groups' => array('groups_child'),
                    ))
                ),
                'validation_groups' => $childGroups
            ));
    }

    /**
     * Check native constraints functionality independs of type of fields
     *
     * @param string $isValid
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function basicConstraintsAction($isValid)
    {
        $data = array(
            array(
                'blank'    => null,
                'notBlank' => 'a',
                'email'    => 'example@google.com',
                'url'      => 'https://www.google.com',
                'regex'    => 'aaa123',
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

        return $this->render(
            'DefaultTestBundle:FunctionalTests:index.html.twig',
            array('form' => $form->createView(), 'isValid' => $isValid)
        );
    }

    /**
     * Check different data-transformers
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function transformersAction()
    {
        $date = new \DateTime();
        $date->setDate(2009, 4, 7);
        $date->setTime(21, 15);

        $blank = new Blank(array('message' => '{{ value }}'));
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
                'repeated'              => 'asdf'
            ), array('js_validation' => true))
            ->add('date', 'date', array('constraints' => array($blank)))
            ->add('time', 'time', array('constraints' => array($blank)))
            ->add('datetime', 'datetime', array('constraints' => array($blank)))
            ->add('checkbox', 'checkbox', array('constraints' => array(new False(array('message' => '{{ value }}')))))
            ->add('radio', 'radio', array('constraints' => array(new True(array('message' => '{{ value }}')))))
            ->add('ChoicesToValues', 'choice', array(
                'multiple'    => true,
                'choices'     => $choices,
                'constraints' => array($blank)
            ))
            ->add('ChoiceToValue', 'choice', array(
                'multiple'    => false,
                'choices'     => $choices,
                'constraints' => array($blank)
            ))
            ->add('ChoicesToBooleanArray', 'choice', array(
                'expanded'    => true,
                'multiple'    => true,
                'choices'     => $choices,
                'constraints' => array($blank)
            ))
            ->add('ChoiceToBooleanArray', 'choice', array(
                'expanded'    => true,
                'multiple'    => false,
                'choices'     => $choices,
                'constraints' => array($blank)
            ))
            ->add('repeated', 'repeated', array(
                'type'            => 'text',
                'invalid_message' => 'not_equal',
                'first_options'   => array('label' => 'Field'),
                'second_options'  => array('label' => 'Repeat Field', 'data' => 'zxcv'),
            ))
            ->getForm();

        $form->handleRequest($this->getRequest());

        return $this->render(
            'DefaultTestBundle:FunctionalTests:index.html.twig',
            array('form' => $form->createView())
        );
    }

    /**
     * Check onvalidate listeners
     *
     * @param string $mode
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function onValidateListenersAction($mode)
    {
        $builder = $this->createFormBuilder(null, array('js_validation' => true));
        $builder
            ->add('name', 'text', array(
                'constraints' => array(
                    new NotBlank(array(
                        'message' => '{{ value }}'
                    ))
                )
            ));

        return $this->render(
            'DefaultTestBundle:FunctionalTests:index.html.twig',
            array(
                'form'               => $builder->getForm()->createView(),
                'checkListeners'     => true,
                'checkListenersMode' => $mode
            )
        );
    }

    public function partOfFormAction()
    {
        $builder = $this->createFormBuilder(null, array('js_validation' => true));
        $builder
            ->add('name', 'text', array(
                'constraints' => array(
                    new NotBlank(array(
                        'message' => 'name_{{ value }}'
                    ))
                )
            ))
            ->add('email', 'text', array(
                'constraints' => array(
                    new NotBlank(array(
                        'message' => '{{ value }}'
                    ))
                )
            ));

        return $this->render(
            'DefaultTestBundle:FunctionalTests:partOfForm.html.twig',
            array(
                'form' => $builder->getForm()->createView(),
            )
        );
    }

    public function emptyElementsAction()
    {
        $builder = $this->createFormBuilder(null, array('js_validation' => true));
        $builder
            ->add('name', 'text', array(
                'constraints' => array(
                    new NotBlank(array(
                        'message' => 'name_{{ value }}'
                    ))
                )
            ))
            ->add('email', 'text', array());

        return $this->render(
            'DefaultTestBundle:FunctionalTests:partOfForm.html.twig',
            array(
                'form' => $builder->getForm()->createView(),
            )
        );
    }
}
