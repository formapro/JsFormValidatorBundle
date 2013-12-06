<?php

namespace Fp\JsFormValidatorBundle\Tests\Factory;

use Fp\JsFormValidatorBundle\Model\JsFormElement;
use Fp\JsFormValidatorBundle\Model\JsValidationData;
use Fp\JsFormValidatorBundle\Tests\BaseTestCase;

/**
 * Class JsFormElementTest
 *
 * @package Fp\JsFormValidatorBundle\Tests\Factory
 */
class JsFormElementTest extends BaseTestCase
{
    /**
     * Test for converting model to an array
     */
    public function testModelToArrayConversion()
    {
        $model = new JsFormElement('id', 'name');
        $array = array(
            'id'                => 'id',
            'name'              => 'name',
            'dataClass'         => null,
            'invalidMessage'    => null,
            'type'              => null,
            'validationData'    => array(),
            'transformers'      => array(),
            'cascadeValidation' => 1,
            'events'            => array(),
            'children'          => array(),
            'config'            => array(),
        );
        $this->assertEquals($array, $model->toArray());
    }

    /**
     * Test for converting model to a string
     */
    public function testModelToStringConversion()
    {
        $model  = new JsFormElement('id', 'form_id');
        $string = "new FpJsFormElement({'id':'id','name':'form_id','dataClass':null,'type':null,'invalidMessage':null,'validationData':[],'transformers':[],'cascadeValidation':true,'events':[],'children':[],'config':[]})";
        $this->assertEquals($string, $model->__toString());
    }

    /**
     * Test for actions with model children
     */
    public function testChildrenActions()
    {
        $parent = new JsFormElement('parent', 'form_id');
        $child  = new JsFormElement('child', 'form_id');
        $parent->setChildren(array('child' => $child));

        $this->assertNotNull($parent->getChild('child'));
        $this->assertNull($parent->getChild('fakeName'));
    }

    /**
     * Test for setting extra-events for Javascript
     */
    public function testJsEvents()
    {
        $model  = new JsFormElement('id', 'form_id');
        $events = array(
            'test_id' => array('event_1', 'event_2')
        );
        $model->setJsEvents($events);
        $this->assertEquals($events, $model->getJsEvents());
    }

    /**
     * Test for actions with validation data
     */
    public function testAddValidationData()
    {
        $model = new JsFormElement('id', 'form_id');
        $data = new JsValidationData(array(), 'data');
        // New data should be added
        $model->addValidationData($data);
        $this->assertCount(1, $model->getValidationData());
        // The same data should be overwrited
        $model->addValidationData($data);
        $this->assertCount(1, $model->getValidationData());
        // An array of two another data should be added
        $model->addValidationData(array(
            new JsValidationData(array(), 'data_2'),
            new JsValidationData(array(), 'data_3')
        ));
        $this->assertCount(3, $model->getValidationData());
        // Other value types should be ignored
        $model->addValidationData('string');
        $this->assertCount(3, $model->getValidationData());
    }
}
