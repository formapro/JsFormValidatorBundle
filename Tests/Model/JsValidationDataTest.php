<?php
/**
 * Created by PhpStorm.
 * User: Yury Maltsev
 * Email: dev.ymalcev@gmail.com
 * Date: 11/11/13
 * Time: 11:06 AM
 */

namespace Fp\JsFormValidatorBundle\Tests\Factory;

use Fp\JsFormValidatorBundle\Model\JsValidationData;
use Fp\JsFormValidatorBundle\Tests\BaseTestCase;

class JsValidationDataTest extends BaseTestCase
{
    public function testConstructor()
    {
        // With groups as an array
        $model = new JsValidationData(array('test_group'), 'test');
        $this->assertEquals(array('test_group', 'Default'), $model->getGroups());
        // With groups as a name of callback
        $model = new JsValidationData('test_group', 'test');
        $this->assertEquals('test_group', $model->getGroups());
    }

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

    public function testModelToStringConversion()
    {
        $model  = new JsValidationData(array(), 'test');
        $string = "new FpJsValidationData({'type':'test','groups':['Default'],'constraints':[],'getters':[]})";
        $this->assertEquals($string, $model->__toString());
    }
}
