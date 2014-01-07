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
        $this->assertArrayHasKey('init_js_validation', $extension->getFunctions());
    }

    /**
     * Test the mail function
     */
    public function testGetJsValidator()
    {
        $factory = $this->getMock(
            'Fp\JsFormValidatorBundle\Factory\JsFormValidatorFactory',
            array('processQueue'),
            array(),
            '',
            false
        );
        $modelOne   = new JsFormElement('id_1', 'form_id_1');
        $modelTwo   = new JsFormElement('id_2', 'form_id_2');
        $factory->expects($this->once())
            ->method('processQueue')
            ->will($this->returnValue(array($modelOne, $modelTwo)));

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
        $model = "<script type=\"text/javascript\">FpJsFormValidatorFactory.initNewModel(" . $modelOne . ");\nFpJsFormValidatorFactory.initNewModel(" . $modelTwo . ");</script>";
        $this->assertEquals($result, $model);
    }
}
