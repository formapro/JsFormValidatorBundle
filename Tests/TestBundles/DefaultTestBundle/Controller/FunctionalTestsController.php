<?php

namespace Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Controller;

use Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Entity\BasicConstraintsEntity;
use Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Entity\CamelCaseEntity;
use Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Entity\CommentEntity;
use Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Entity\CustomizationEntity;
use Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Entity\EmptyChoiceEntity;
use Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Entity\PasswordFieldEntity;
use Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Entity\TagEntity;
use Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Entity\TaskEntity;
use Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Entity\UniqueEntity;
use Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Form\AsyncLoadType;
use Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Form\BasicConstraintsEntityType;
use Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Form\CustomizationType;
use Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Form\EmptyChoiceType;
use Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Form\PasswordFieldType;
use Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Form\TaskType;
use Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Form\UniqueType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\EqualTo;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\IdenticalTo;
use Symfony\Component\Validator\Constraints\Ip;
use Symfony\Component\Validator\Constraints\IsFalse;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\LessThan;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotEqualTo;
use Symfony\Component\Validator\Constraints\NotIdenticalTo;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\Time;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\Url;


/**
 * Class FunctionalTestsController
 *
 * @package Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Controller
 */
class FunctionalTestsController extends BaseTestController
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function phpinfoAction() {
        ob_start();
        phpinfo();

        return $this->render(
            'DefaultTestBundle:FunctionalTests:phpinfo.html.twig',
            array('phpinfo' => ob_get_clean())
        );
    }

    /**
     * @param Request $request
     * @param         $controller
     * @param         $type
     * @param         $js
     *
     * @return mixed
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function baseAction(Request $request, $controller, $type, $js)
    {
        $method = $controller . 'Action';
        if (method_exists($this, $method)) {
            return $this->{$method}($request, $type, $js);
        } else {
            throw $this->createNotFoundException('Action not found');
        }
    }

    /**
     * Check translation service
     * /fp_js_form_validator/javascript_unit_test/translations/{domain}/{js}
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param string                                    $domain
     * @param                                           $js
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function translationsAction(
        Request $request,
        /** @noinspection PhpUnusedParameterInspection */
        $domain,
        $js
    ) {
        $constraint = function ($msg) {
            return array('constraints' => array(new NotBlank(array('message' => $msg))));
        };

        $form = $this
            ->createFormBuilder(null, array('js_validation' => (bool)$js))
            ->add('name', TextType::class, $constraint('blank.translation'))
            ->getForm();

        $form->handleRequest($request);

        return $this->render(
            'DefaultTestBundle:FunctionalTests:index.html.twig',
            array(
                'form'     => $form->createView(),
                'extraMsg' => $request->isMethod('post') ? 'passed' : '',
            )
        );
    }

    /**
     * Check groups and nested forms
     * /fp_js_form_validator/javascript_unit_test/nesting/{type}/{js}
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param string                                    $type
     * @param string                                    $js
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function nestingAction(Request $request, $type, $js)
    {
        switch ($type) {
            case 'array':
                $form = $this->getSimpleForm(array('groups_array'), (bool)$js);
                break;
            case 'callback':
                $form = $this->getSimpleForm(
                    function () {
                        return array('groups_callback');
                    },
                    (bool)$js
                );
                break;
            case 'nested':
                $form = $this->getNestedForm(true, array('groups_child'), (bool)$js, true);
                break;
            case 'nested_no_groups':
                $form = $this->getNestedForm(true, array(), (bool)$js);
                break;
            case 'nested_no_cascade':
                $form = $this->getNestedForm(false, array(), (bool)$js);
                break;
            default:
                $form = $this->getSimpleForm(array(), (bool)$js);
                break;
        }

        $form->handleRequest($request);

        return $this->render(
            'DefaultTestBundle:FunctionalTests:index.html.twig',
            array('form' => $form->createView())
        );
    }

    /**
     * Check a unique constraint
     * /fp_js_form_validator/javascript_unit_test/unique_entity/{isValid}/{js}
     *
     * @param Request $request
     * @param         $isValid
     * @param         $js
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function unique_entityAction(Request $request, $isValid, $js)
    {
        $entity = new UniqueEntity();

        if (false == (bool)$isValid) {
            $entity->setEmail('existing_email');
            $entity->setName('existing_name');
        } else {
            $entity->setTitle('test');
        }

        $form = $this->createForm(UniqueType::class, $entity, array('js_validation' => (bool)$js));
        $form->handleRequest($request);

        return $this->render(
            'DefaultTestBundle:FunctionalTests:index.html.twig',
            array(
                'form'       => $form->createView(),
                'extraMsg'   => $form->isValid() ? 'unique_entity_valid' : '',
                'onValidate' => true,
            )
        );
    }

    /**
     * Check native constraints functionality independently of type of fields
     * /fp_js_form_validator/javascript_unit_test/basic_constraints/{isValid}/{js}
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param string                                    $isValid
     * @param string                                    $js
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function basic_constraintsAction(Request $request, $isValid, $js)
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

        $data   = $isValid ? $data[0] : $data[1];
        $entity = new BasicConstraintsEntity();
        $entity->populate($data);
        $entity->isValid = (bool)$isValid;
        $form            = $this->createForm(
            BasicConstraintsEntityType::class,
            $entity,
            array('js_validation' => (bool)$js)
        );
        $form->handleRequest($request);

        return $this->render(
            'DefaultTestBundle:FunctionalTests:index.html.twig',
            array('form' => $form->createView(), 'isValid' => $isValid)
        );
    }

    /**
     * Check different data-transformers
     * /fp_js_form_validator/javascript_unit_test/transformers/{isValid}/{js}
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param                                           $isValid
     * @param                                           $js
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function transformersAction(Request $request, $isValid, $js)
    {
        $date = new \DateTime();
        $date->setDate(2009, 4, 7);
        $date->setTime(21, 15);

        $choices = array('m' => 'male', 'f' => 'female');

        $form = $this
            ->createFormBuilder(
                array(
                    'date'                  => $date,
                    'time'                  => $date,
                    'datetime'              => $date,
                    'checkbox'              => (bool)$isValid,
                    'radio'                 => (bool)$isValid,
                    'ChoicesToValues'       => array('m', 'f'),
                    'ChoiceToValue'         => 'f',
                    'ChoicesToBooleanArray' => array('m', 'f'),
                    'ChoiceToBooleanArray'  => 'f',
                    'repeated'              => 'asdf'
                ),
                array('js_validation' => (bool)$js)
            )
            ->add('date', DateType::class, array('constraints' => array(new Date())))
            ->add('time', TimeType::class, array('constraints' => array(new Time())))
            ->add('datetime', DateTimeType::class, array('constraints' => array(new DateTime())))
            ->add(
                'checkbox',
                CheckboxType::class,
                array(
                    'constraints' => array(
                        new IsTrue(array(
                            'message' => 'checkbox_false'
                        )),
                        new IsFalse(array(
                            'message' => 'checkbox_true'
                        ))
                    )
                )
            )
            ->add(
                'radio',
                RadioType::class,
                array(
                    'constraints' => array(
                        new IsTrue(array(
                            'message' => 'radio_false'
                        )),
                        new IsFalse(array(
                            'message' => 'radio_true'
                        ))
                    )
                )
            )
            ->add(
                'ChoicesToValues',
                ChoiceType::class,
                array(
                    'multiple'    => true,
                    'choices'     => $choices,
                    'constraints' => array(
                        new Choice(
                            array(
                                'multiple'   => true,
                                'choices'    => array_keys($choices),
                                'maxMessage' => 'multiple_choices',
                                'max'        => $isValid ? 10 : 1
                            )
                        )
                    )
                )
            )
            ->add(
                'ChoiceToValue',
                ChoiceType::class,
                array(
                    'multiple'    => false,
                    'choices'     => $choices,
                    'constraints' => array(
                        new Choice(
                            array(
                                'multiple' => false,
                                'choices'  => $isValid ? array_keys($choices) : array('a', 'b'),
                                'message'  => 'single_choice',
                            )
                        )
                    )
                )
            )
            ->add(
                'ChoicesToBooleanArray',
                ChoiceType::class,
                array(
                    'expanded'    => true,
                    'multiple'    => true,
                    'choices'     => $choices,
                    'constraints' => array(
                        new Choice(
                            array(
                                'multiple'        => true,
                                'choices'         => $isValid ? array_keys($choices) : array('m', 'c'),
                                'multipleMessage' => 'multiple_boolean_choices'
                            )
                        )
                    )
                )
            )
            ->add(
                'ChoiceToBooleanArray',
                ChoiceType::class,
                array(
                    'expanded'    => true,
                    'multiple'    => false,
                    'choices'     => $choices,
                    'constraints' => array(
                        new Choice(
                            array(
                                'multiple' => false,
                                'choices'  => $isValid ? array_keys($choices) : array('m', 'c'),
                                'message'  => 'single_boolean_choice'
                            )
                        )
                    )
                )
            )
            ->add(
                'repeated',
                RepeatedType::class,
                array(
                    'type'            => TextType::class,
                    'invalid_message' => 'not_equal',
                    'first_options'   => array('label' => 'Field'),
                    'second_options'  => array('label' => 'Repeat Field', 'data' => $isValid ? 'asdf' : 'zxcv'),
                )
            )
            ->getForm();

        $form->handleRequest($request);

        return $this->render(
            'DefaultTestBundle:FunctionalTests:index.html.twig',
            array('form' => $form->createView())
        );
    }

    /**
     * Set several elements, but show not all of them
     * /fp_js_form_validator/javascript_unit_test/part/default/{js}
     *
     * @param Request $request
     * @param         $type
     * @param         $js
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function partAction(
        Request $request,
        /** @noinspection PhpUnusedParameterInspection */
        $type,
        $js
    ) {
        $constraint = function ($msg) {
            return array('constraints' => array(new NotBlank(array('message' => $msg))));
        };

        $form = $this
            ->createFormBuilder(null, array('js_validation' => (bool)$js))
            ->add('name', TextType::class, $constraint('name_value'))
            ->add('email', TextType::class, $constraint('{{ value }}'))
            ->getForm();

        $form->handleRequest($request);

        return $this->render(
            'DefaultTestBundle:FunctionalTests:partOfForm.html.twig',
            array(
                'form' => $form->createView(),
            )
        );
    }

    /**
     * Contains an element with no constrains
     * Also checks some constrains to ignore empty value
     * /fp_js_form_validator/javascript_unit_test/empty/-/{js}
     *
     * @param Request $request
     * @param         $type
     * @param         $js
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function emptyAction(
        Request $request,
        /** @noinspection PhpUnusedParameterInspection */
        $type,
        $js
    ) {
        $builder = $this->createFormBuilder(null, array('js_validation' => (bool)$js));
        $form    = $builder
            ->add(
                'name',
                TextType::class,
                array(
                    'constraints' => array(
                        new Email(array('message' => 'wrong_email')),
                        new EqualTo(array('value' => 'asdf', 'message' => 'wrong_equal_to')),
                        new IsFalse(array('message' => 'wrong_false')),
                        new GreaterThan(array('value' => 5, 'message' => 'wrong_greater_than')),
                        new GreaterThanOrEqual(array('value' => 5, 'message' => 'wrong_greater_than_or_equal')),
                        new IdenticalTo(array('value' => 5, 'message' => 'wrong_identical_to')),
                        new Ip(array('message' => 'wrong_ip')),
                        new Length(array('min' => 5, 'minMessage' => 'wrong_length')),
                        new LessThan(array('value' => -5, 'message' => 'wrong_less_than')),
                        new LessThanOrEqual(array('value' => -5, 'message' => 'wrong_less_than_or_equal')),
                        new NotEqualTo(array('value' => 5, 'message' => 'wrong_not_equal_to')),
                        new NotIdenticalTo(array('value' => 5, 'message' => 'wrong_not_identical_to')),
                        new Range(
                            array(
                                'min'            => 1,
                                'max'            => 5,
                                'minMessage'     => 'wrong_min_range',
                                'maxMessage'     => 'wrong_ax_range',
                                'invalidMessage' => 'wrong_invalid_range'
                            )
                        ),
                        new Time(array('message' => 'wrong_time')),
                        new Date(array('message' => 'wrong_date')),
                        new DateTime(array('message' => 'wrong_date_time')),
                        new IsTrue(array('message' => 'wrong_true')),
                        new Type(array('type' => 'integer', 'message' => 'wrong_type')),
                        new Url(array('message' => 'wrong_url')),
                    )
                )
            )
            ->add('email', TextType::class, array())
            ->getForm();

        $form->handleRequest($request);

        return $this->render(
            'DefaultTestBundle:FunctionalTests:partOfForm.html.twig',
            array(
                'form' => $form->createView(),
            )
        );
    }

    /**
     * Check that fields can be disabled on the JS side
     * /fp_js_form_validator/javascript_unit_test/disable/{type}/{js}
     *
     * @param Request $request
     * @param string  $type
     * @param         $js
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function disableAction(Request $request, $type, $js)
    {
        $constraint = function ($msg) {
            return array('constraints' => array(new NotBlank(array('message' => $msg))));
        };

        $builder = $this
            ->createFormBuilder(null, array('js_validation' => (bool)$js))
            ->add('enabled', TextType::class, $constraint('enabled_field'));

        switch ($type) {
            case 'global':
                break;
            case 'field':
                $builder->add('disabled', TextType::class, $constraint('disabled_field'));
                break;
            default:
                break;
        }

        $form = $builder->getForm();
        $form->handleRequest($request);

        return $this->render(
            'DefaultTestBundle:FunctionalTests:index.html.twig',
            array(
                'form'     => $form->createView(),
                'extraMsg' => $request->isMethod('post') ? 'disabled_validation' : ''
            )
        );
    }

    public function sub_requestAction(
        /** @noinspection PhpUnusedParameterInspection */
        Request $request,
        /** @noinspection PhpUnusedParameterInspection */
        $type,
        $js
    ) {
        return $this->render(
            'DefaultTestBundle:FunctionalTests:requestWithSubRequest.html.twig',
            array(
                'param_js' => $js,
            )
        );
    }

    public function subRequestIncludedAction(Request $request, $js)
    {
        $constraint = function ($msg) {
            return array('constraints' => array(new NotBlank(array('message' => $msg))));
        };

        $form = $this
            ->createFormBuilder(null, array('js_validation' => (bool)$js))
            ->add('name', TextType::class, $constraint('enabled_field'))
            ->getForm();

        $form->handleRequest($request);

        return $this->render(
            'DefaultTestBundle:FunctionalTests:subRequest.html.twig',
            array(
                'form'     => $form->createView(),
                'extraMsg' => $request->isMethod('post') ? 'disabled_validation' : '',
            )
        );
    }

    public function camelcaseAction(
        Request $request,
        /** @noinspection PhpUnusedParameterInspection */
        $type,
        $js
    ) {
        $entity = new CamelCaseEntity();
        $form   = $this->createFormBuilder($entity, array('js_validation' => (bool)$js))
            ->add('camel_case_field')
            ->add('camelCaseField')
            ->add('submit', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        return $this->render(
            'DefaultTestBundle:FunctionalTests:index.html.twig',
            array(
                'form' => $form->createView(),
            )
        );
    }

    public function customizationAction(Request $request, $type, $js)
    {
        $entity = new CustomizationEntity();
        $form   = $this->createForm(CustomizationType::class, $entity, array('js_validation' => (bool)$js));

        $form->handleRequest($request);

        $tpl = "DefaultTestBundle:FunctionalTests:customization_{$type}.html.twig";

        return $this->render($tpl, array('form' => $form->createView()));
    }

    public function customUniqueEntityControllerAction(Request $request)
    {
        $entity = new UniqueEntity();
        $entity->setEmail('existing_email');
        $entity->setName('existing_name');

        $form = $this->createForm(UniqueType::class, $entity);
        $form->handleRequest($request);
        $tpl = 'DefaultTestBundle:FunctionalTests:index.html.twig';

        return $this->render($tpl, array('form' => $form->createView()));
    }

    public function collectionAction(Request $request, $isValid, $js)
    {
        $task = new TaskEntity();
        $task->addTag(new TagEntity());
        $task->addComment(new CommentEntity());

        $form = $this->createForm(
            TaskType::class,
            $task,
            array(
                'attr' => array(
                    'novalidate' => 'novalidate',
                ),
                'js_validation' => (bool)$js
            )
        );

        $form->handleRequest($request);
        $tpl = 'DefaultTestBundle:FunctionalTests:collection.html.twig';

        return $this->render(
            $tpl,
            array(
                'form'     => $form->createView(),
                'extraMsg' => $request->isMethod('post') ? 'done' : '',
            )
        );
    }

    public function empty_choiceAction(Request $request, $isValid, $js)
    {
        $entity = new EmptyChoiceEntity();

        if ((bool)$isValid) {
            $entity->setCity('london');
            $entity->setCountries(array('france'));
            $entity->setContinent('europe');
        }
        $form = $this->createForm(
            EmptyChoiceType::class,
            $entity,
            array(
                'js_validation' => (bool)$js
            )
        );

        $form->handleRequest($request);
        $tpl = 'DefaultTestBundle:FunctionalTests:empty_choice.html.twig';

        return $this->render(
            $tpl,
            array(
                'form'     => $form->createView(),
                'extraMsg' => $request->isMethod('post') ? 'done' : '',
            )
        );
    }

    public function password_fieldAction(Request $request, $isValid, $js)
    {
        $entity = new PasswordFieldEntity();

        if ((bool)$isValid) {
            $entity->setPassword('test_pass');
        }

        $form   = $this->createForm(
            PasswordFieldType::class,
            $entity,
            array(
                'js_validation' => (bool)$js
            )
        );

        $form->handleRequest($request);
        $tpl = 'DefaultTestBundle:FunctionalTests:empty_choice.html.twig';

        return $this->render(
            $tpl,
            array(
                'form'     => $form->createView(),
                'extraMsg' => $request->isMethod('post') ? 'done' : '',
            )
        );
    }

    public function async_loadAction(Request $request, $isValid, $js)
    {
        $passForm = $isValid;
        $onLoad   = $js;
        $form     = $this->createForm(AsyncLoadType::class, null, array('attr' => array('novalidate' => true)));
        $form->handleRequest($request);

        if ('1' == $passForm) {
            $passForm = $form->createView();
        } elseif ('0' == $passForm) {
            $passForm = null;
        }

        return $this->render(
            'DefaultTestBundle:FunctionalTests:async_load.html.twig',
            array(
                'form'     => $form->createView(),
                'extraMsg' => $request->isMethod('post') ? 'done' : '',
                'passForm' => $passForm,
                'onLoad'    => (bool)$onLoad,
            )
        );
    }
}
