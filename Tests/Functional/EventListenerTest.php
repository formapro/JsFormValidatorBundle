<?php

namespace Fp\JsFormValidatorBundle\Tests\Form;

use Fp\JsFormValidatorBundle\Tests\BaseTestCase;
use Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Form\TestFormType;
use Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Form\TestFormTypeValidationFalse;

/**
 * Class EventListenerTest
 *
 * @package Fp\JsFormValidatorBundle\Tests\Factory
 */
class EventListenerTest extends BaseTestCase
{
    /**
     * Test the method JsFormValidatorFactory::translateConstraint()
     */
    public function testListener()
    {
        $client = $this->createClient();
        $formFactory = $client->getContainer()->get('form.factory');

        // Check that the second form will override the first form
        // Check that the third form will not be processed
        $formTrue = $formFactory->create(new TestFormType());
        $formTrue->add('email', 'text');
        $formFactory->create(new TestFormType());
        $formFactory->create(new TestFormTypeValidationFalse());

        $result = $client->getContainer()->get('fp_js_form_validator.factory')->processQueue();
        $this->assertCount(1, $result);
        $this->assertCount(1, $result[0]->getChildren());

        $formFactory->create(new TestFormType());
        $formTrue = $formFactory->create(new TestFormType());
        $formTrue->add('email', 'text');

        $result = $client->getContainer()->get('fp_js_form_validator.factory')->processQueue();
        $this->assertCount(1, $result);
        $this->assertCount(2, $result[0]->getChildren());
    }
}
