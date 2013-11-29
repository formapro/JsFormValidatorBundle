<?php
/**
 * Created by PhpStorm.
 * User: Yury Maltsev
 * Email: dev.ymalcev@gmail.com
 * Date: 11/11/13
 * Time: 11:06 AM
 */

namespace Fp\JsFormValidatorBundle\Tests\Factory;

use Fp\JsFormValidatorBundle\Model\JsModelAbstract;
use Fp\JsFormValidatorBundle\Tests\BaseTestCase;
use Fp\JsFormValidatorBundle\Tests\Fixtures\SimpleObject;
use Fp\JsFormValidatorBundle\Tests\Fixtures\SimpleToStringObject;

class JsModelAbstractTest extends BaseTestCase
{
    /**
     * @return JsModelAbstract
     */
    protected function getModelMock()
    {
        $model = $this->getMockForAbstractClass('Fp\JsFormValidatorBundle\Model\JsModelAbstract');
        $model->expects($this->any())
            ->method('toArray')
            ->will($this->returnValue(array(
                'toStringObject' => new SimpleToStringObject(),
                'simpleObject'   => new SimpleObject(),
                'assocArray'     => array('a' => 'b'),
                'sequentArray'   => array('a', 'b'),
                'string'         => 'string',
                'true'           => true,
                'false'          => false,
                'integer'        => 10,
                'float'          => 10.5,
                'null'           => null,
                'resource'       => fopen(__DIR__ . '/JsModelAbstractTest.php', 'r')
            )));

        return $model;
    }

    public function testToArray()
    {
        $this->assertCount(11, $this->getModelMock()->toArray());
    }

    public function testToStringAndOutputFormat()
    {
        $model = $this->getModelMock();

        $this->assertEquals(JsModelAbstract::OUTPUT_FORMAT_JAVASCRIPT, $model->getOutputFormat());
        $string = "{'toStringObject':'toStringName','simpleObject':{'name':'John'},'assocArray':{'a':'b'},'sequentArray':['a','b'],'string':'string','true':true,'false':false,'integer':10,'float':10.5,'null':null,'resource':undefined}";
        $this->assertEquals($string, $this->getModelMock()->__toString());

        $model->setOutputFormat(JsModelAbstract::OUTPUT_FORMAT_JSON);
        $this->assertEquals(JsModelAbstract::OUTPUT_FORMAT_JSON, $model->getOutputFormat());
        $string = '{"toStringObject":{},"simpleObject":{"name":"John"},"assocArray":{"a":"b"},"sequentArray":["a","b"],"string":"string","true":true,"false":false,"integer":10,"float":10.5,"null":null,"resource":null}';
        $this->assertEquals($string, $model->__toString());
    }
}
