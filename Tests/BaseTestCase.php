<?php

namespace Fp\JsFormValidatorBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class BaseTestCase
 *
 * @package Fp\JsFormValidatorBundle\Tests
 */
class BaseTestCase extends WebTestCase
{
    /**
     * Open no public methods
     *
     * @param object $obj
     * @param string $methodName
     * @param array  $args
     *
     * @return mixed
     */
    protected function callNoPublicMethod($obj, $methodName, array $args = array())
    {
        $class  = new \ReflectionClass($obj);
        $method = $class->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($obj, $args);
    }

    /**
     * @param $name
     *
     * @return object
     */
    protected function getService($name)
    {
        return $this->getContainer()->get($name);
    }

    /**
     * @return Container
     */
    protected function getContainer()
    {
        if (!self::$kernel) {
            self::bootKernel();
        }

        return self::$kernel->getContainer();
    }
} 