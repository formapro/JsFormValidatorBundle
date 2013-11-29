<?php
/**
 * Created by PhpStorm.
 * User: Yury Maltsev
 * Email: dev.ymalcev@gmail.com
 * Date: 10/24/13
 * Time: 5:06 PM
 */
namespace Fp\JsFormValidatorBundle\Tests\Twig\Extension;

use Fp\JsFormValidatorBundle\Model\JsFormElement;
use Fp\JsFormValidatorBundle\Tests\BaseTestCase;
use Fp\JsFormValidatorBundle\Twig\Extension\JsFormValidatorTwigExtension;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\Forms;

class JsFormValidatorTwigExtensionTest extends BaseTestCase
{
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

    public function testGetName()
    {
        $extension = $this->getMock(
            'Fp\JsFormValidatorBundle\Twig\Extension\JsFormValidatorTwigExtension',
            null,
            array(),
            '',
            false
        );

        /** @var JsFormValidatorTwigExtension $extension */
        $this->assertEquals('fp_js_form_validator', $extension->getName());
    }

    public function testGetJsValidator()
    {
        $factory = $this->getMock(
            'Fp\JsFormValidatorBundle\Factory\JsFormValidatorFactory',
            array('createJsModel', 'generateInlineJs'),
            array(),
            '',
            false
        );
        $model   = new JsFormElement('id', 'form_id');
        $factory->expects($this->once())
            ->method('createJsModel')
            ->will($this->returnValue($model));
        $factory->expects($this->once())
            ->method('generateInlineJs')
            ->will($this->returnArgument(0));

        $extension = $this->getMock(
            'Fp\JsFormValidatorBundle\Twig\Extension\JsFormValidatorTwigExtension',
            array('getFactory'),
            array(),
            '',
            false
        );
        $extension->expects($this->exactly(2))
            ->method('getFactory')
            ->will($this->returnValue($factory));

        /** @var JsFormValidatorTwigExtension $extension */
        $formFactory = Forms::createFormFactory();
        /** @var Form $form */
        $form = $formFactory->create('text');
        /** @var JsFormElement $result */
        $result = $extension->getJsValidator($form);
        $this->assertEquals(spl_object_hash($model), spl_object_hash($result));
    }
}
