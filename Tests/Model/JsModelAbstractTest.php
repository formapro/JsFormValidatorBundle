<?php

namespace Fp\JsFormValidatorBundle\Tests\Model;

use Fp\JsFormValidatorBundle\Model\JsModelAbstract;
use Fp\JsFormValidatorBundle\Tests\BaseTestCase;
use Fp\JsFormValidatorBundle\Tests\Fixtures\SimpleObject;
use Fp\JsFormValidatorBundle\Tests\Fixtures\SimpleToStringObject;

/**
 * Class JsModelAbstractTest
 *
 * @package Fp\JsFormValidatorBundle\Tests\Factory
 */
//TODO: should be changed due to new requirements
class JsModelAbstractTest// extends BaseTestCase
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
        $model = $this->getMockForAbstractClass('Fp\JsFormValidatorBundle\Model\JsModelAbstract');
        $model->expects($this->once())
            ->method('toArray')
            ->will(
                $this->returnValue(
                    array(
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
                    )
                )
            );

        /** @var JsModelAbstract $model */
        $result = $model->toArray();
        //$a = $this->getModelMock()->toArray();
        //$this->assertCount(10, $model->toArray());
    }

    /**
     * Test for converting model to a string
     * Test for changing the output format
     */
    public function testToStringAndOutputFormat()
    {
        $model = $this->getModelMock();
        $extraData = array('file' => fopen(__DIR__.'/JsModelAbstractTest.php', 'r'));

        $string = "{'toStringObject':'toStringName','simpleObject':{'name':'John'},'assocArray':{'a':'b'},'sequentArray':['a','b'],'string':'string','true':true,'false':false,'integer':10,'float':10.5,'null':null,'file':undefined}";
//        $this->assertEquals($string, $this->getModelMock($extraData)->__toString());
//        $this->assertEquals($string, $model->__toString());
    }
}
