<?php

namespace Fp\JsFormValidatorBundle\Tests\Form;

use Fp\JsFormValidatorBundle\Model\JsFormElement;
use Fp\JsFormValidatorBundle\Tests\BaseTestCase;
use Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Form\TestFormType;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class EventListenerTest
 *
 * @package Fp\JsFormValidatorBundle\Tests\Form
 */
class SwitchingValidationTest extends BaseTestCase
{
    /**
     * Test with the global option
     */
    public function testGlobalValidation()
    {
        $client = $this->createClient();
        $formFactory = $client->getContainer()->get('form.factory');
        $fpFactory = $client->getContainer()->get('fp_js_form_validator.factory');

        $config = $fpFactory->getConfig();
        $config['js_validation'] = true;
        $reflectionClass = new \ReflectionClass($fpFactory);
        $reflectionProperty = $reflectionClass->getProperty('config');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($fpFactory, $config);

        $formFactory
            ->createNamedBuilder('form_null', 'form', null)
            ->add('name_null', 'text', array('constraints' => new NotBlank()))
            ->getForm();

        $formFactory
            ->createNamedBuilder('form_true', 'form', null, array('js_validation' => true))
            ->add('name_true', new TestFormType())
            ->getForm();

        $formFactory
            ->createNamedBuilder('form_false', 'form', null, array('js_validation' => false))
            ->add('name_false')
            ->getForm();

        /** @var JsFormElement[] $result */
        $result = $fpFactory->processQueue();

        $this->assertCount(2, $result);
        $this->assertEquals('form_null', $result[0]->getName());
        $this->assertEquals('form_true', $result[1]->getName());
    }

    /**
     * Test without the global enabled
     */
    public function testLocalValidation()
    {
        $client = $this->createClient();
        $formFactory = $client->getContainer()->get('form.factory');
        $fpFactory = $client->getContainer()->get('fp_js_form_validator.factory');

        $formFactory
            ->createNamedBuilder('form_null', 'form', null)
            ->add('name_null')
            ->getForm();

        $formFactory
            ->createNamedBuilder('form_true', 'form', null, array('js_validation' => true))
            ->add('name_true', new TestFormType())
            ->getForm();

        $formFactory
            ->createNamedBuilder('form_false', 'form', null, array('js_validation' => false))
            ->add('name_false')
            ->getForm();

        /** @var JsFormElement[] $result */
        $result = $fpFactory->processQueue();

        $this->assertCount(1, $result);
        $this->assertEquals('form_true', $result[0]->getName());
    }

    /**
     * Test without the global enabled
     */
    public function testGlobalDisabledValidation()
    {
        $client = $this->createClient();
        $formFactory = $client->getContainer()->get('form.factory');
        $fpFactory = $client->getContainer()->get('fp_js_form_validator.factory');

        $config = $fpFactory->getConfig();
        $config['js_validation'] = false;
        $reflectionClass = new \ReflectionClass($fpFactory);
        $reflectionProperty = $reflectionClass->getProperty('config');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($fpFactory, $config);

        $formFactory
            ->createNamedBuilder('form_true', 'form', null, array('js_validation' => true))
            ->add('name_true', new TestFormType())
            ->getForm();

        $formFactory
            ->createNamedBuilder('form_false', 'form', null, array('js_validation' => false))
            ->add('name_true', new TestFormType())
            ->getForm();

        $result = $fpFactory->processQueue();

        $this->assertCount(0, $result);
    }

    /**
     * Test without the global enabled
     */
    public function testEnableFieldsValidation()
    {
        $client      = $this->createClient();
        $formFactory = $client->getContainer()->get('form.factory');
        $fpFactory   = $client->getContainer()->get('fp_js_form_validator.factory');
        $constr      = array(new NotBlank(array('message' => 'name_{{ value }}')));

        $formFactory
            ->createNamedBuilder('form_null', 'form', null, array()) // disabled
            ->add('name_null_null', 'text', array('constraints' => $constr)) // disabled
            ->add('name_null_true', 'text', array('js_validation' => true, 'constraints' => $constr)) // enabled as separated element
            ->add('name_null_false', 'text', array('js_validation' => false, 'constraints' => $constr)) // disabled
            ->getForm();

        $formFactory
            ->createNamedBuilder('form_true', 'form', null, array('js_validation' => true)) // enabled as separated element
            ->add('name_true_null', 'text', array('constraints' => $constr)) // enabled
            ->add('name_true_true', 'text', array('js_validation' => true, 'constraints' => $constr)) // enabled
            ->add('name_true_false', 'text', array('js_validation' => false, 'constraints' => $constr)) // disabled
            ->getForm();

        /** @var JsFormElement[] $result */
        $result = $fpFactory->processQueue();

        $this->assertCount(2, $result);

        $this->assertEquals('name_null_true', $result[0]->getName());

        $this->assertEquals('form_true', $result[1]->getName());
        $children = $result[1]->getChildren();
        $this->assertCount(2, $children);
        $this->assertEquals('name_true_null', $children['name_true_null']->getName());
        $this->assertEquals('name_true_true', $children['name_true_true']->getName());
    }
}
