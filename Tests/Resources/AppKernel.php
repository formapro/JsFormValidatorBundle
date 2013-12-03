<?php
namespace Fp\JsFormValidatorBundle\Tests;

use Fp\JsFormValidatorBundle\FpJsFormValidatorBundle;
use Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\DefaultTestBundle;
use Symfony\Bundle\AsseticBundle\AsseticBundle;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Behat\MinkBundle\MinkBundle;


class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new FpJsFormValidatorBundle(),
            new FrameworkBundle(),
            new SecurityBundle(),
            new TwigBundle(),
            new AsseticBundle(),
            new DoctrineBundle(),
            new MinkBundle(),

            new DefaultTestBundle()
        );

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config.php');
    }

    public function init()
    {
    }

    public function loadClassCache($name = 'classes', $extension = '.php')
    {
    }
}