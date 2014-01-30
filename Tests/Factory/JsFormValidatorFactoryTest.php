<?php

namespace Fp\JsFormValidatorBundle\Tests\Factory;

use Fp\JsFormValidatorBundle\Factory\JsFormValidatorFactory;
use Fp\JsFormValidatorBundle\Tests\Fixtures\FormGroupsArray;
use Fp\JsFormValidatorBundle\Tests\Fixtures\FormGroupsClosure;
use Fp\JsFormValidatorBundle\Tests\Fixtures\TestConstraint;
use Fp\JsFormValidatorBundle\Tests\Fixtures\TestForm;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceList;
use Symfony\Component\Form\Extension\Core\DataTransformer\ArrayToPartsTransformer;
use Symfony\Component\Form\Extension\Core\DataTransformer\ChoicesToValuesTransformer;
use Symfony\Component\Form\Extension\Core\DataTransformer\DataTransformerChain;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToArrayTransformer;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\Forms;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * Class JsFormValidatorFactoryTest
 *
 * @package Fp\JsFormValidatorBundle\Tests\Factory
 */

//TODO: should be changed due to new requirements
class JsFormValidatorFactoryTest// extends BaseTestCase
{
    /**
     * @var string
     */
    protected $testTransMessage = 'translated_message';

    /**
     * Test the method JsFormValidatorFactory::translateConstraint()
     */
    public function testTranslateConstraint()
    {
        $factory = $this->getMock(
            'Fp\JsFormValidatorBundle\Factory\JsFormValidatorFactory',
            array('translateMessage'),
            array(),
            '',
            false
        );
        $factory->expects($this->exactly(3))
            ->method('translateMessage')
            ->will($this->returnValue($this->testTransMessage));
        $constraint = new TestConstraint();
        /** @var TestConstraint $constraint */
        $constraint = $this->callNoPublicMethod($factory, 'translateConstraint', array($constraint));
        // Translator should translate only those properties, which name contains the "message" substring
        $this->assertEquals($this->testTransMessage, $constraint->errorMessage, 'The "errorMessage" option is recognized as message');
        $this->assertEquals($this->testTransMessage, $constraint->messageError, 'That the "messageError" option is recognized as message');
        $this->assertEquals($this->testTransMessage, $constraint->someMessageError, 'That the "someMessageError" option is recognized as message');
        $this->assertNotEquals($this->testTransMessage, $constraint->value, 'That the "value" option is NOT recognized as message');
    }

    /**
     * Test the method JsFormValidatorFactory::parseConstraints()
     */
    public function testParseConstraints()
    {
        $factory = $this->getMock(
            'Fp\JsFormValidatorBundle\Factory\JsFormValidatorFactory',
            array('translateConstraint'),
            array(),
            '',
            false
        );
        $factory->expects($this->exactly(2))
            ->method('translateConstraint')
            ->will($this->returnArgument(0));

        $notBlankConstraint = new NotBlank();
        $notBlankName       = get_class($notBlankConstraint);
        $testConstraint     = new TestConstraint();
        $testName           = get_class($testConstraint);
        $constraints        = array($notBlankConstraint, $testConstraint);

        $result = $this->callNoPublicMethod($factory, 'parseConstraints', array($constraints));
        $this->assertInstanceOf($notBlankName, $result[$notBlankName][0], 'The native Symfony constraint was parsed successfully');
        $this->assertInstanceOf($testName, $result[$testName][0], 'Our custom constraint was parsed successfully');
    }

    /**
     * Test the method JsFormValidatorFactory::parseGetters()
     */
    public function testParseGetters()
    {
        $factory = $this->getMock(
            'Fp\JsFormValidatorBundle\Factory\JsFormValidatorFactory',
            array('parseConstraints'),
            array(),
            '',
            false
        );
        // Return constraints as is
        // Should be called once with class metadata only
        $factory->expects($this->exactly(2))
            ->method('parseConstraints')
            ->will($this->returnArgument(0));

        $metadata = new ClassMetadata('Fp\JsFormValidatorBundle\Tests\Fixtures\Entity');
        $metadata->addGetterConstraint('nameLegal', new TestConstraint());
        $metadata->addGetterConstraint('fileLegal', new NotBlank());

        $result = $this->callNoPublicMethod($factory, 'parseGetters', array($metadata->getters));
        $this->assertCount(1, $result['nameLegal']['constraints'], 'The first getter was parsed successfully and has all the passed constraints');
        $this->assertCount(1, $result['fileLegal']['constraints'], 'The second getter was parsed successfully and has all the passed constraints');
    }

    /**
     * Test the method JsFormValidatorFactory::parseTransformers()
     */
    public function testParseTransformers()
    {
        $factory = $this->getMock(
            'Fp\JsFormValidatorBundle\Factory\JsFormValidatorFactory',
            array(),
            array(),
            '',
            false
        );

        // Create transformers collection
        $chain      = array(
            new ArrayToPartsTransformer(array()),
            new DateTimeToArrayTransformer()
        );
        $chain      = new DataTransformerChain($chain);
        $simple     = new DateTimeToArrayTransformer();
        $collection = array($chain, $simple);

        $trans = $this->callNoPublicMethod($factory, 'parseTransformers', array($collection));
        // Receive two transformers
        $this->assertCount(2, $trans, 'All the transformers were parsed successfully');
        // The first one is a chain contains two items
        $chain = new DataTransformerChain(array());
        $this->assertEquals(get_class($chain), $trans[0]['name'], 'Datatransformer chain has correct name');
        $this->assertCount(2, $trans[0]['transformers'], 'Datatransformer chain has two transformers');
        // The secons one is a simple transformer
        $this->assertArrayNotHasKey('transformers', $trans[1], 'The second transformer is not parsed as a chain');
    }

    /**
     * Test the method JsFormValidatorFactory::getTransformersList()
     */
    public function testGetTransformersList()
    {
        $factory = $this->getMock(
            'Fp\JsFormValidatorBundle\Factory\JsFormValidatorFactory',
            array('parseTransformers'),
            array(),
            '',
            false
        );
        // Return transformers as is
        // Should be called twice for the "Form" and "array" types only
        $factory->expects($this->once())
            ->method('parseTransformers')
            ->will($this->returnArgument(0));
        // Get from a Form element
        $formFactory = Forms::createFormFactory();
        $form        = $formFactory->create('datetime');
        $this->assertCount(1, $this->callNoPublicMethod($factory, 'getTransformersList', array($form)), 'Were received all the necessary transformers');
    }

    /**
     * Test the method JsFormValidatorFactory::isProcessableElement()
     */
    public function testIsProcessableElement()
    {
        $factory     = $this->getMock(
            'Fp\JsFormValidatorBundle\Factory\JsFormValidatorFactory',
            array('parseTransformers'),
            array(),
            '',
            false
        );
        $formFactory = Forms::createFormFactory();
        // Not for buttons
        $element = $formFactory->create('button');
        $this->assertFalse($this->callNoPublicMethod($factory, 'isProcessableElement', array($element)), 'Buttons should NOT be processed');
        // Not for hiddens
        $element = $formFactory->create('hidden');
        $this->assertFalse($this->callNoPublicMethod($factory, 'isProcessableElement', array($element)), 'Hidden inputs should NOT be processed');
        // Just for forms
        $element = $formFactory->create('text');
        $this->assertTrue($this->callNoPublicMethod($factory, 'isProcessableElement', array($element)), 'Forms should be processed');
    }

    /**
     * Test the method JsFormValidatorFactory::hasMetadata()
     */
    public function testHasMetadata()
    {
        $factory     = $this->getMock(
            'Fp\JsFormValidatorBundle\Factory\JsFormValidatorFactory',
            array(),
            array(),
            '',
            false
        );
        $formFactory = Forms::createFormFactory();

        // Check element with metadata
        $element = $formFactory->create('file');
        $this->assertTrue($this->callNoPublicMethod($factory, 'hasMetadata', array($element)), 'The "file" element has metadata');
        // Check element without metadata
        $element = $formFactory->create('text');
        $this->assertFalse($this->callNoPublicMethod($factory, 'hasMetadata', array($element)), 'The "text" element has NOT metadata');
    }

    /**
     * Test the method JsFormValidatorFactory::getValidationGroups()
     */
    public function testGetValidationGroups()
    {
        $factory     = $this->getMock(
            'Fp\JsFormValidatorBundle\Factory\JsFormValidatorFactory',
            array(),
            array(),
            '',
            false
        );
        $formFactory = Forms::createFormFactory();
        // Without groups
        $form = $formFactory->create(new TestForm());
        $this->assertCount(0, $this->callNoPublicMethod($factory, 'getValidationGroups', array($form)), 'This form should NOT have validation groups');
        // Groups as an array
        $form = $formFactory->create(new FormGroupsArray());
        $this->assertCount(1, $this->callNoPublicMethod($factory, 'getValidationGroups', array($form)), 'This form should have validation groups as array');
        // Groups as an function
        $formType = new FormGroupsClosure();
        $formName = get_class($formType);
        $form     = $formFactory->create($formType);
        $this->assertEquals($formName, $this->callNoPublicMethod($factory, 'getValidationGroups', array($form)), 'This form should have validation groups as closure');
    }

    /**
     * Test the method JsFormValidatorFactory::getEntityMetadata()
     */
    public function testGetEntityMetadata()
    {
        $factory = $this->getMock(
            'Fp\JsFormValidatorBundle\Factory\JsFormValidatorFactory',
            array('getMetadataFor'),
            array(),
            '',
            false
        );
        $factory->expects($this->once())
            ->method('getMetadataFor')
            ->will($this->returnValue(true));
        $formFactory = Forms::createFormFactory();

        // Check element with metadata
        $element = $formFactory->create('file');
        $this->assertTrue($this->callNoPublicMethod($factory, 'getEntityMetadata', array($element)), 'The "file" element has metadata');
        // Check element without metadata
        $element = $formFactory->create('text');
        $this->assertNull($this->callNoPublicMethod($factory, 'getEntityMetadata', array($element)), 'The "text" element has NOT metadata');
    }

    /**
     * Test the method JsFormValidatorFactory::processChildren()
     */
    public function testProcessChildren()
    {
        $factory = $this->getMock(
            'Fp\JsFormValidatorBundle\Factory\JsFormValidatorFactory',
            array('createJsModel', 'getEntityMetadata'),
            array(),
            '',
            false
        );
        $factory->expects($this->exactly(2))
            ->method('createJsModel')
            ->will($this->returnCallback(function($child, $childMetadata, $groups){
                if (null === $childMetadata) {
                    return 'null_metadata';
                } else {
                    return 'has_metadata';
                }
            }));

        $factory->expects($this->exactly(2))
            ->method('getEntityMetadata')
            ->will($this->returnArgument(0));

        $formFactory = Forms::createFormFactory();
        $form        = $formFactory->create(new TestForm());

        $metadata = new ClassMetadata('Fp\JsFormValidatorBundle\Tests\Fixtures\Entity');
        $metadata->addPropertyConstraint('name', new TestConstraint());

        $result = $this->callNoPublicMethod($factory, 'processChildren', array($form, $metadata, array()));

        $this->assertEquals($result['name'], 'has_metadata', 'The "name" child is processed');
        $this->assertEquals($result['file'], 'null_metadata', 'The "file" child is processed');
        $this->assertFalse(isset($result['save']), 'The "save" child is NOT processed');
    }

    /**
     * Test the method JsFormValidatorFactory::getMappingValidationData()
     */
    public function testGetMappingValidationData()
    {
        $factory = $this->getMock(
            'Fp\JsFormValidatorBundle\Factory\JsFormValidatorFactory',
            array('parseConstraints', 'parseGetters'),
            array(),
            '',
            false
        );
        $factory->expects($this->exactly(3))
            ->method('parseConstraints')
            ->will($this->returnArgument(0));
        $factory->expects($this->exactly(3))
            ->method('parseGetters')
            ->will($this->returnArgument(0));

        $metadata = new ClassMetadata('Fp\JsFormValidatorBundle\Tests\Fixtures\Entity');
        $metadata->addConstraint(new UniqueEntity(array('fields' => 'name')));
        $metadata->addPropertyConstraint('name', new TestConstraint());
        $metadata->addGetterConstraint('nameLegal', new TestConstraint());
        $metadata->addGetterConstraint('fileLegal', new NotBlank());

        // Check for a ClassMetadata
        /** @var JsValidationData $data */
        $data = $this->callNoPublicMethod($factory, 'getMappingValidationData', array($metadata, array('test')));
        $this->assertCount(1, $data->getConstraints(), '');
        $this->assertCount(2, $data->getGetters(), 'The "ClassMetadata" has two getters');
        $this->assertCount(2, $data->getGroups(), 'The "ClassMetadata" has two groups');

        // Check for an array of ProperyMetadata's
        $metadata = $metadata->getMemberMetadatas('name');
        /** @var JsValidationData[] $aData */
        $aData = $this->callNoPublicMethod($factory, 'getMappingValidationData', array($metadata, array()));
        $this->assertCount(1, $aData, 'Were parsed one metadata form an array');
        $this->assertCount(1, $aData[0]->getConstraints(), 'The parsed medatada has one constraint');
        $this->assertCount(0, $aData[0]->getGetters(), 'The parsed medatada has NOT getters');
        $this->assertCount(1, $aData[0]->getGroups(), 'The parsed medatada has one group');

        // Check for a ProperyMetadata
        $metadata = $metadata[0];
        /** @var JsValidationData $data */
        $data = $this->callNoPublicMethod($factory, 'getMappingValidationData', array($metadata, array()));
        $this->assertCount(1, $data->getConstraints(), 'The "ProperyMetadata" has one constraint');

        // Check for null
        $this->assertCount(0, $this->callNoPublicMethod($factory, 'getMappingValidationData', array(null, array())), 'Neither metadata has not been parsed');
    }

    /**
     * Test the method JsFormValidatorFactory::getElementValidationData()
     */
    public function testGetElementValidationData()
    {
        $factory = $this->getMock(
            'Fp\JsFormValidatorBundle\Factory\JsFormValidatorFactory',
            array('parseConstraints'),
            array(),
            '',
            false
        );
        $factory->expects($this->once())
            ->method('parseConstraints')
            ->will($this->returnArgument(0));

        $formFactory = Forms::createFormFactory();
        $form        = $formFactory->create('text');

        //------------------------------------------------------------------------------------------------------------//
        // Actually, formFactory in local mode does not see the 'constraints' option for some reason
        // So, we need to pass at least one constraint to form using the \ReflectionClass
        $config                 = $form->getConfig();
        $options                = $config->getOptions();
        $options['constraints'] = array(
            new TestConstraint()
        );
        $refObject              = new \ReflectionClass('Symfony\Component\Form\FormConfigBuilder');
        $refProperty            = $refObject->getProperty('options');
        $refProperty->setAccessible(true);
        $refProperty->setValue($config, $options);

        $refObject   = new \ReflectionClass('Symfony\Component\Form\Form');
        $refProperty = $refObject->getProperty('config');
        $refProperty->setAccessible(true);
        $refProperty->setValue($form, $config);
        //------------------------------------------------------------------------------------------------------------//

        $data = $this->callNoPublicMethod($factory, 'getElementValidationData', array($form, array()));

        $this->assertInstanceOf('Fp\JsFormValidatorBundle\Model\JsValidationData', $data[0], 'Successfully received the JsValidationData object');
        $this->assertCount(1, $data[0]->getConstraints(), 'Data has on constraint');
        $this->assertCount(0, $data[0]->getGetters(), 'Data has NOT getters');
        $this->assertCount(1, $data[0]->getGroups(), 'Data has one group');
    }

    /**
     * Test the method JsFormValidatorFactory::createJsModel()
     */
    public function testCreateJsModel()
    {
        $factory = $this->getMock(
            'Fp\JsFormValidatorBundle\Factory\JsFormValidatorFactory',
            array('getConfig', 'getPreparedConfig', 'processChildren', 'getEntityMetadata', 'getMappingValidationData', 'getElementValidationData', 'getTransformersList'),
            array(),
            '',
            false
        );

        $factory->expects($this->never())
            ->method('getConfig')
            ->will($this->returnValue(array('js_validation' => true)));

        $factory->expects($this->exactly(3))
            ->method('getPreparedConfig')
            ->will($this->returnValue(array()));

        $factory->expects($this->exactly(3))
            ->method('processChildren')
            ->will($this->returnArgument(1));

        $factory->expects($this->exactly(1))
            ->method('getEntityMetadata')
            ->will($this->returnValue('entity_metadata'));

        $factory->expects($this->exactly(4))
            ->method('getMappingValidationData')
            ->will($this->returnArgument(0));

        $factory->expects($this->exactly(3))
            ->method('getElementValidationData')
            ->will($this->returnValue(array()));

        $factory->expects($this->exactly(3))
            ->method('getTransformersList')
            ->will($this->returnValue(array()));

        $metadata = new ClassMetadata('Fp\JsFormValidatorBundle\Tests\Fixtures\Entity');
        $metadata->addPropertyConstraint('name', new TestConstraint());

        $formFactory = Forms::createFormFactory();
        /** @var JsFormValidatorFactory $factory */

        // Check with the element consists metadata and Class metadata
        /** @var Form $form */
        $form  = $formFactory->create('file');
        $model = $factory->createJsModel($form, $metadata, array());
        $this->assertEquals('entity_metadata', $model->getChildren(), 'Has been parsed as the parent element with its own metada');

        // Check with child element and Class metadata
        $form  = $formFactory->create(new TestForm())->get('name');
        $model = $factory->createJsModel($form, $metadata, array());
        /** @var ClassMetadata $data */
        $data = $model->getChildren();
        $this->assertEquals('Fp\JsFormValidatorBundle\Tests\Fixtures\Entity', $data->getClassName(), 'Has been parsed as the child element, metada is received from the parent metadata (the first level child)');

        // Check with child element and not Class metadata
        $model = $factory->createJsModel($form, $metadata->getMemberMetadatas('name'), array());
        $this->assertNull($model->getChildren(), 'Has been parsed as third-level child without metadata');
    }

    /**
     * Test the method JsFormValidatorFactory::getTransformerParam()
     */
    public function testGetTransformerParam()
    {
        $factory = $this->getMock(
            'Fp\JsFormValidatorBundle\Factory\JsFormValidatorFactory',
            null,
            array(),
            '',
            false
        );
        $transformer = new ChoicesToValuesTransformer(new ChoiceList(array('a', 'b'), array('A', 'B')));
        $result = $this->callNoPublicMethod($factory, 'getTransformerParam', array($transformer, 'choiceList'));
        $this->assertEquals(array('a', 'b'), $result, 'All the transformer\'s parameres have been received');
    }

    /**
     * Test the method JsFormValidatorFactory::prepareConfig()
     */
    public function testPrepareConfig()
    {
        $factory = $this->getMock(
            'Fp\JsFormValidatorBundle\Factory\JsFormValidatorFactory',
            array('generateUrl'),
            array(),
            '',
            false
        );
        $factory->expects($this->exactly(2))
            ->method('generateUrl')
            ->will($this->returnValue('generated_url'));

        $config = array(
            'routing' => array(
                'param_one' => 'route_one',
                'param_two' => 'route_two'
            ),
            'some_other_config' => true
        );
        $expected = array(
            'routing' => array(
                'param_one' => 'generated_url',
                'param_two' => 'generated_url'
            )
        );

        $refObject   = new \ReflectionObject($factory);
        $refProperty = $refObject->getProperty('config');
        $refProperty->setAccessible(true);
        $refProperty->setValue($factory, $config);

        // Check prepared config
        $this->assertEquals($expected, $this->callNoPublicMethod($factory, 'getPreparedConfig', array()), 'The config has been prepared successfully');
    }

    /**
     * Check if factory has the correct config
     */
    public function testGetConfig()
    {
        $client = static::createClient();
        /** @var JsFormValidatorFactory $factory */
        $factory = $client->getContainer()->get('fp_js_form_validator.factory');

        $defaultConfig = array(
            'translation_domain' => 'validators',
            'routing' => array(
                'check_unique_entity' => 'fp_js_form_validator.check_unique_entity'
            ),
            'js_validation' => true
        );

        $this->assertEquals($defaultConfig, $factory->getConfig(), 'Check the bundle config');
    }
}
