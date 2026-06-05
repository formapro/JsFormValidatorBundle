<?php

namespace Fp\JsFormValidatorBundle\Tests\Unit;

use Fp\JsFormValidatorBundle\Factory\JsFormValidatorFactory;
use Fp\JsFormValidatorBundle\Twig\Extension\JsFormValidatorTwigExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormView;

class JsFormValidatorTwigExtensionTest extends TestCase
{
    public function testRegistersTwigFunctions()
    {
        $extension = new JsFormValidatorTwigExtension($this->createMock(JsFormValidatorFactory::class));

        $functions = $extension->getFunctions();

        $this->assertCount(2, $functions);
        $this->assertSame('init_js_validation', $functions[0]->getName());
        $this->assertSame('js_validator_config', $functions[1]->getName());
        $this->assertSame(array('html'), $functions[0]->getSafe(new \Twig\Node\Node()));
        $this->assertSame(array('html'), $functions[1]->getSafe(new \Twig\Node\Node()));
    }

    public function testConfigDelegatesToFactory()
    {
        $factory = $this->createMock(JsFormValidatorFactory::class);
        $factory
            ->expects($this->once())
            ->method('getJsConfigString')
            ->willReturn('<script>config</script>')
        ;

        $extension = new JsFormValidatorTwigExtension($factory);

        $this->assertSame('<script>config</script>', $extension->getConfig());
    }

    public function testValidatorAcceptsFormViewAndWrapsScriptByDefault()
    {
        $view = new FormView();
        $view->vars['name'] = 'profile';

        $factory = $this->createMock(JsFormValidatorFactory::class);
        $factory
            ->expects($this->once())
            ->method('getJsValidatorString')
            ->with('profile', false)
            ->willReturn('FpJsFormValidator.addModel({}, false);')
        ;

        $extension = new JsFormValidatorTwigExtension($factory);

        $this->assertSame(
            '<script type="text/javascript">FpJsFormValidator.addModel({}, false);</script>',
            $extension->getJsValidator($view, false)
        );
    }

    public function testValidatorCanReturnRawJavascript()
    {
        $factory = $this->createMock(JsFormValidatorFactory::class);
        $factory
            ->expects($this->once())
            ->method('getJsValidatorString')
            ->with('profile', true)
            ->willReturn('raw-js')
        ;

        $extension = new JsFormValidatorTwigExtension($factory);

        $this->assertSame('raw-js', $extension->getJsValidator('profile', true, false));
    }
}
