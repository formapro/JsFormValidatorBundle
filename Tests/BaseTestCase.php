<?php
/**
 * Created by PhpStorm.
 * User: Yury Maltsev
 * Email: dev.ymalcev@gmail.com
 * Date: 11/14/13
 * Time: 10:17 AM
 */

namespace Fp\JsFormValidatorBundle\Tests;


use Behat\MinkBundle\Test\MinkTestCase;
use Doctrine\ORM\EntityManager;

class BaseTestCase extends  MinkTestCase {
    /**
     * Open no public methods
     *
     * @param string $obj
     * @param string $methodName
     * @param array $args
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