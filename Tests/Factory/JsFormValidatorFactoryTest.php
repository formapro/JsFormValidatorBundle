<?php

namespace Fp\JsFormValidatorBundle\Tests\Factory;

use Fp\JsFormValidatorBundle\Factory\JsFormValidatorFactory;

/**
 * Class JsFormValidatorFactoryTest
 */
class JsFormValidatorFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var JsFormValidatorFactory
     */
    protected $factory;

    /**
     * Sets up a new test factory instance.
     */
    public function setUp()
    {
        $this->factory = $this->getMockBuilder('Fp\JsFormValidatorBundle\Factory\JsFormValidatorFactory')
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Tears down test class properties.
     */
    public function tearDown()
    {
        $this->factory = null;
    }

    /**
     * Tests the getValidationGroups() method when returning an empty array.
     */
    public function testGetValidationGroupsWhenEmpty()
    {
        // Given
        $formConfig = $this->getMock('Symfony\Component\Form\FormConfigInterface');
        $formConfig
            ->expects($this->once())
            ->method('getOption')
            ->with($this->equalTo('validation_groups'))
            ->will($this->returnValue(null));

        $form = $this->getMockBuilder('Symfony\Component\Form\Form')
            ->disableOriginalConstructor()
            ->getMock();

        $form->expects($this->once())->method('getConfig')->will($this->returnValue($formConfig));

        $factory = new \ReflectionMethod($this->factory, 'getValidationGroups');
        $factory->setAccessible(true);

        // When
        $result = $factory->invoke($this->factory, $form);

        // Then
        $this->assertEquals(array('Default'), $result, 'Should return Default as validation_groups');
    }

    /**
     * Tests the getValidationGroups() method when using a simple array.
     */
    public function testGetValidationGroupsWhenArray()
    {
        // Given
        $formConfig = $this->getMock('Symfony\Component\Form\FormConfigInterface');
        $formConfig
            ->expects($this->once())
            ->method('getOption')
            ->with($this->equalTo('validation_groups'))
            ->will($this->returnValue(array('test1', 'test2')));

        $form = $this->getMockBuilder('Symfony\Component\Form\Form')
            ->disableOriginalConstructor()
            ->getMock();

        $form->expects($this->once())->method('getConfig')->will($this->returnValue($formConfig));

        $factory = new \ReflectionMethod($this->factory, 'getValidationGroups');
        $factory->setAccessible(true);

        // When
        $result = $factory->invoke($this->factory, $form);

        // Then
        $this->assertEquals(array('test1', 'test2'), $result, 'Should return the validation_groups array');
    }

    /**
     * Tests the getValidationGroups() method when using a Closure.
     */
    public function testGetValidationGroupsWhenClosure()
    {
        // Given
        $formConfig = $this->getMock('Symfony\Component\Form\FormConfigInterface');
        $formConfig
            ->expects($this->once())
            ->method('getOption')
            ->with($this->equalTo('validation_groups'))
            ->will($this->returnValue(function () { return array('person'); }));

        $form = $this->getMockBuilder('Symfony\Component\Form\Form')
            ->disableOriginalConstructor()
            ->getMock();

        $form->expects($this->once())->method('getConfig')->will($this->returnValue($formConfig));

        $factory = new \ReflectionMethod($this->factory, 'getValidationGroups');
        $factory->setAccessible(true);

        // When
        $result = $factory->invoke($this->factory, $form);

        // Then
        $this->assertEquals(array('person'), $result, 'Should return the closure response as validation_groups');
    }
}