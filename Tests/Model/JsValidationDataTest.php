<?php

namespace Fp\JsFormValidatorBundle\Tests\Factory;

use Fp\JsFormValidatorBundle\Model\JsValidationData;
use Fp\JsFormValidatorBundle\Tests\BaseTestCase;

/**
 * Class JsValidationDataTest
 *
 * @package Fp\JsFormValidatorBundle\Tests\Factory
 */
class JsValidationDataTest extends BaseTestCase
{
    /**
     * Test the cosntructor
     */
    public function testConstructor()
    {
        // With groups as an array
        $model = new JsValidationData(array('test_group'), 'test');
        $this->assertEquals(array('test_group', 'Default'), $model->getGroups());
        // With groups as a name of callback
        $model = new JsValidationData('test_group', 'test');
        $this->assertEquals('test_group', $model->getGroups());
    }

    /**
     * Test for converting model to an array
     */
    public function testModelToArrayConversion()
    {
        $model = new JsValidationData(array(), 'test');
        $array = array(
            'type'        => 'test',
            'groups'      => array('Default'),
            'constraints' => array(),
            'getters'     => array()
        );
        $this->assertEquals($array, $model->toArray());
    }

    /**
     * Test for converting model to a string
     */
    public function testModelToStringConversion()
    {
        $model  = new JsValidationData(array(), 'test');
        $string = "new FpJsValidationData({'type':'test','groups':['Default'],'constraints':[],'getters':[]})";
        $this->assertEquals($string, $model->__toString());
    }
}
