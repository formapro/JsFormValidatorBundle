<?php

namespace Fp\JsFormValidatorBundle\Tests\Twig\Extension;

use Fp\JsFormValidatorBundle\Model\JsFormElement;
use Fp\JsFormValidatorBundle\Tests\BaseTestCase;
use Fp\JsFormValidatorBundle\Twig\Extension\JsFormValidatorTwigExtension;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\Forms;

/**
 * Class JsFormValidatorTwigExtensionTest
 *
 * @package Fp\JsFormValidatorBundle\Tests\Twig\Extension
 */
class JsFormValidatorTwigExtensionTest extends BaseTestCase
{
    /**
     * Test functions list
     */
    public function testGetFunctions()
    {
        $extension = $this->getMock(
            'Fp\JsFormValidatorBundle\Twig\Extension\JsFormValidatorTwigExtension',
            null,
            array(),
            '',
            false
        );

        /** @var JsFormValidatorTwigExtension $extension */
        $this->assertArrayHasKey('fp_jsfv', $extension->getFunctions());
    }

    /**
     * Test the mail function
     */
    public function testGetJsValidator()
    {
        $factory = $this->getMock(
            'Fp\JsFormValidatorBundle\Factory\JsFormValidatorFactory',
            array('createJsModel'),
            array(),
            '',
            false
        );
        $model   = new JsFormElement('id', 'form_id');
        $factory->expects($this->once())
            ->method('createJsModel')
            ->will($this->returnValue($model));

        $extension = $this->getMock(
            'Fp\JsFormValidatorBundle\Twig\Extension\JsFormValidatorTwigExtension',
            array('getFactory'),
            array(),
            '',
            false
        );
        $extension->expects($this->once())
            ->method('getFactory')
            ->will($this->returnValue($factory));

        /** @var JsFormValidatorTwigExtension $extension */
        $formFactory = Forms::createFormFactory();
        /** @var Form $form */
        $form = $formFactory->create('text');
        /** @var JsFormElement $result */
        $result = $extension->getJsValidator($form);
        $model = "<script type=\"text/javascript\">FpJsFormValidatorFactory.initNewModel(" . $model . ")</script>";
        $this->assertEquals($result, $model);
    }
}
