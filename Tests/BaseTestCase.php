<?php

namespace Fp\JsFormValidatorBundle\Tests;

use Behat\MinkBundle\Test\MinkTestCase;

/**
 * Class BaseTestCase
 *
 * @package Fp\JsFormValidatorBundle\Tests
 */
class BaseTestCase extends  MinkTestCase
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
} 