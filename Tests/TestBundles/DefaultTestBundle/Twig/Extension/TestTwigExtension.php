<?php

namespace Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Twig\Extension;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Kernel;

/**
 * Class JsFormValidatorTwigExtension
 *
 * @package Fp\JsFormValidatorBundle\Twig\Extension
 */
class TestTwigExtension extends \Twig_Extension
{
    /**
     * @var Kernel
     */
    protected $kernel;

    public function __construct($kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('update_js_lib', array($this, 'updateJsLib')),
        );
    }

    /**
     * @return string
     */
    public function updateJsLib()
    {
        $response = $this->kernel->handle(Request::create('/js/fp_js_validator.js'));
        $libFile  = $this->kernel->getRootDir() . '/../../Resources/public/js/fp_js_validator.js';
        file_put_contents($libFile, $response->getContent());
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     * @codeCoverageIgnore
     */
    public function getName()
    {
        return 'fp_js_validator.test_extension';
    }
}
