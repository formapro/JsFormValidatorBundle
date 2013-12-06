<?php

namespace Fp\JsFormValidatorBundle\Tests\Factory;

use Fp\JsFormValidatorBundle\Model\JsModelAbstract;
use Fp\JsFormValidatorBundle\Tests\BaseTestCase;
use Fp\JsFormValidatorBundle\Tests\Fixtures\SimpleObject;
use Fp\JsFormValidatorBundle\Tests\Fixtures\SimpleToStringObject;

/**
 * Class JsModelAbstractTest
 *
 * @package Fp\JsFormValidatorBundle\Tests\Factory
 */
class JsModelAbstractTest extends BaseTestCase
{
    /**
     * @param array $extraData
     *
     * @return JsModelAbstract
     */
    protected function getModelMock(array $extraData = array())
    {
        $data = array_merge(array(
            'toStringObject' => new SimpleToStringObject(),
            'simpleObject'   => new SimpleObject(),
            'assocArray'     => array('a' => 'b'),
            'sequentArray'   => array('a', 'b'),
            'string'         => 'string',
            'true'           => true,
            'false'          => false,
            'integer'        => 10,
            'float'          => 10.5,
            'null'           => null
        ), $extraData);

        $model = $this->getMockForAbstractClass('Fp\JsFormValidatorBundle\Model\JsModelAbstract');
        $model->expects($this->any())
            ->method('toArray')
            ->will($this->returnValue($data));

        return $model;
    }

    /**
     * Test for converting model to an array
     */
    public function testToArray()
    {
        $this->assertCount(10, $this->getModelMock()->toArray());
    }

    /**
     * Test for converting model to a string
     * Test for changing the output format
     */
    public function testToStringAndOutputFormat()
    {
        $model = $this->getModelMock();
        $extraData = array('file' => fopen(__DIR__.'/JsModelAbstractTest.php', 'r'));

        $this->assertEquals(JsModelAbstract::OUTPUT_FORMAT_JAVASCRIPT, $model->getOutputFormat());
        $string = "{'toStringObject':'toStringName','simpleObject':{'name':'John'},'assocArray':{'a':'b'},'sequentArray':['a','b'],'string':'string','true':true,'false':false,'integer':10,'float':10.5,'null':null,'file':undefined}";
        $this->assertEquals($string, $this->getModelMock($extraData)->__toString());

        $model->setOutputFormat(JsModelAbstract::OUTPUT_FORMAT_JSON);
        $this->assertEquals(JsModelAbstract::OUTPUT_FORMAT_JSON, $model->getOutputFormat());
        $string = '{"toStringObject":{},"simpleObject":{"name":"John"},"assocArray":{"a":"b"},"sequentArray":["a","b"],"string":"string","true":true,"false":false,"integer":10,"float":10.5,"null":null}';
        $this->assertEquals($string, $model->__toString());
    }
}
