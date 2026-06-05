<?php

namespace Fp\JsFormValidatorBundle\Tests\Unit;

use Fp\JsFormValidatorBundle\Model\JsModelAbstract;
use PHPUnit\Framework\TestCase;

class JsModelAbstractTest extends TestCase
{
    public function testConvertsScalarValuesToJavascriptLiterals()
    {
        $this->assertSame('null', JsModelAbstract::phpValueToJs(null));
        $this->assertSame('true', JsModelAbstract::phpValueToJs(true));
        $this->assertSame('false', JsModelAbstract::phpValueToJs(false));
        $this->assertSame('42', JsModelAbstract::phpValueToJs(42));
        $this->assertSame('13.5', JsModelAbstract::phpValueToJs(13.5));
        $this->assertSame("'42'", JsModelAbstract::phpValueToJs('42'));
        $this->assertSame("'O\\'Reilly\\\\book'", JsModelAbstract::phpValueToJs("O'Reilly\\book"));
    }

    public function testConvertsArraysAndObjectsRecursively()
    {
        $value = array(
            'quote\'key' => array('first', false),
            'nested' => (object) array('path' => 'src\\Model'),
        );

        $this->assertSame(
            "{'quote\\'key':['first',false],'nested':{'path':'src\\\\Model'}}",
            JsModelAbstract::phpValueToJs($value)
        );
        $this->assertSame("[1,'two',null]", JsModelAbstract::phpValueToJs(array(1, 'two', null)));
    }

    public function testConvertsStringableObjectsAndUnsupportedValues()
    {
        $resource = fopen('php://memory', 'rb');

        $this->assertSame("'stringable-value'", JsModelAbstract::phpValueToJs(new StringableFixture()));
        $this->assertSame('undefined', JsModelAbstract::phpValueToJs($resource));

        fclose($resource);
    }

    public function testModelSerializesItsPublicProperties()
    {
        $model = new JsModelFixture();
        $model->name = 'profile';
        $model->enabled = true;
        $model->children = array(new NestedJsModelFixture());

        $array = $model->toArray();

        $this->assertSame('profile', $array['name']);
        $this->assertTrue($array['enabled']);
        $this->assertCount(1, $array['children']);
        $this->assertInstanceOf(NestedJsModelFixture::class, $array['children'][0]);
        $this->assertSame(
            "{'name':'profile','enabled':true,'children':[{'value':'nested'}]}",
            $model->toJsString()
        );
        $this->assertSame($model->toJsString(), (string) $model);
    }
}

class JsModelFixture extends JsModelAbstract
{
    public $name;

    public $enabled;

    public $children = array();
}

class NestedJsModelFixture extends JsModelAbstract
{
    public $value = 'nested';
}

class StringableFixture
{
    public function __toString()
    {
        return 'stringable-value';
    }
}
