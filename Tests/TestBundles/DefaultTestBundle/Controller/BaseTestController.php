<?php
namespace Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Controller;

use Symfony\Component\Validator\Constraints\NotEqualTo;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Form\TestSubFormType;
use Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Form\TestFormType;
use Symfony\Component\Form\Form;
use Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Entity\TestEntity;

class BaseTestController extends  Controller {
    /**
     * @param Form $form
     *
     * @return array
     */
    protected function getGroups($form)
    {
        $result             = array();
        $result['groups']   = $form->getConfig()->getOption('validation_groups');
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

    protected function getNestedForm($cascade, $childGroups, $js, $bubbling = false)
    {
        return $this
            ->createForm(
                new TestFormType(),
                new TestEntity(),
                array(
                    'validation_groups'  => array('groups_array'),
                    'cascade_validation' => $cascade,
                    'js_validation'      => $js,
                )
            )
            ->add(
                'email',
                new TestSubFormType(),
                array(
                    'error_bubbling'    => $bubbling,
                    'constraints'       => array(
                        new Type(array(
                            'type'    => 'integer',
                            'message' => 'child_groups_array_message',
                            'groups'  => array('groups_array'),
                        )),
                        new Type(array(
                            'type'    => 'integer',
                            'message' => 'child_groups_child_message',
                            'groups'  => array('groups_child'),
                        ))
                    ),
                    'validation_groups' => $childGroups
                )
            );
    }

    protected function notEqualConstraint($type, $value)
    {
        return array(
            'constraints' => array(
                new NotEqualTo(array(
                    'value' => $value,
                    'message' => $type . '_{{ value }}'
                ))
            )
        );
    }
}