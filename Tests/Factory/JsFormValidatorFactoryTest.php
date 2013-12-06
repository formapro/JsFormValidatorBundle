<?php

namespace Fp\JsFormValidatorBundle\Tests\Factory;

use Fp\JsFormValidatorBundle\Factory\JsFormValidatorFactory;
use Fp\JsFormValidatorBundle\Model\JsValidationData;
use Fp\JsFormValidatorBundle\Tests\BaseTestCase;
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
class JsFormValidatorFactoryTest extends BaseTestCase
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
        $this->assertEquals($this->testTransMessage, $constraint->errorMessage);
        $this->assertEquals($this->testTransMessage, $constraint->messageError);
        $this->assertEquals($this->testTransMessage, $constraint->someMessageError);
        $this->assertNotEquals($this->testTransMessage, $constraint->value);
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
        $this->assertInstanceOf($notBlankName, $result[$notBlankName][0]);
        $this->assertInstanceOf($testName, $result[$testName][0]);
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
        $this->assertCount(1, $result['nameLegal']['constraints']);
        $this->assertCount(1, $result['fileLegal']['constraints']);
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
        $this->assertCount(2, $trans);
        // The first one is a chain contains two items
        $chain = new DataTransformerChain(array());
        $this->assertEquals(get_class($chain), $trans[0]['name']);
        $this->assertCount(2, $trans[0]['transformers']);
        // The secons one is a simple transformer
        $this->assertArrayNotHasKey('transformers', $trans[1]);
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
        $this->assertCount(1, $this->callNoPublicMethod($factory, 'getTransformersList', array($form)));
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
        $this->assertFalse($this->callNoPublicMethod($factory, 'isProcessableElement', array($element)));
        // Not for hiddens
        $element = $formFactory->create('hidden');
        $this->assertFalse($this->callNoPublicMethod($factory, 'isProcessableElement', array($element)));
        // Just for forms
        $element = $formFactory->create('text');
        $this->assertTrue($this->callNoPublicMethod($factory, 'isProcessableElement', array($element)));
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
        $this->assertTrue($this->callNoPublicMethod($factory, 'hasMetadata', array($element)));
        // Check element without metadata
        $element = $formFactory->create('text');
        $this->assertFalse($this->callNoPublicMethod($factory, 'hasMetadata', array($element)));
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
        $this->assertCount(0, $this->callNoPublicMethod($factory, 'getValidationGroups', array($form)));
        // Groups as an array
        $form = $formFactory->create(new FormGroupsArray());
        $this->assertCount(1, $this->callNoPublicMethod($factory, 'getValidationGroups', array($form)));
        // Groups as an function
        $formType = new FormGroupsClosure();
        $formName = get_class($formType);
        $form     = $formFactory->create($formType);
        $this->assertEquals($formName, $this->callNoPublicMethod($factory, 'getValidationGroups', array($form)));
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
        $this->assertTrue($this->callNoPublicMethod($factory, 'getEntityMetadata', array($element)));
        // Check element without metadata
        $element = $formFactory->create('text');
        $this->assertNull($this->callNoPublicMethod($factory, 'getEntityMetadata', array($element)));
    }

    /**
     * Test the method JsFormValidatorFactory::processChildren()
     */
    public function testProcessChildren()
    {
        $factory = $this->getMock(
            'Fp\JsFormValidatorBundle\Factory\JsFormValidatorFactory',
            array('createJsModel'),
            array(),
            '',
            false
        );
        $factory->expects($this->exactly(2))
            ->method('createJsModel')
            ->will($this->returnArgument(1));
        $formFactory = Forms::createFormFactory();
        $form        = $formFactory->create(new TestForm());

        $metadata = new ClassMetadata('Fp\JsFormValidatorBundle\Tests\Fixtures\Entity');
        $metadata->addPropertyConstraint('name', new TestConstraint());

        $result = $this->callNoPublicMethod($factory, 'processChildren', array($form, $metadata, array()));

        $this->assertNotNull($result['name']);
        $this->assertNull($result['file']);
        $this->assertFalse(isset($result['save']));
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
        $factory->expects($this->exactly(1))
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
        $this->assertCount(1, $data->getConstraints());
        $this->assertCount(2, $data->getGetters());
        $this->assertCount(2, $data->getGroups());

        // Check for an array
        $metadata = $metadata->getMemberMetadatas('name');
        /** @var JsValidationData[] $aData */
        $aData = $this->callNoPublicMethod($factory, 'getMappingValidationData', array($metadata, array()));
        $this->assertCount(1, $aData);
        $this->assertCount(1, $aData[0]->getConstraints());
        $this->assertCount(0, $aData[0]->getGetters());
        $this->assertCount(1, $aData[0]->getGroups());

        // Check for a ProperyMetadata
        $metadata = $metadata[0];
        /** @var JsValidationData $data */
        $data = $this->callNoPublicMethod($factory, 'getMappingValidationData', array($metadata, array()));
        $this->assertCount(1, $data->getConstraints());

        // Check for null
        $this->assertCount(0, $this->callNoPublicMethod($factory, 'getMappingValidationData', array(null, array())));
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

        $this->assertCount(1, $data->getConstraints());
        $this->assertCount(0, $data->getGetters());
        $this->assertCount(1, $data->getGroups());
    }

    /**
     * Test the method JsFormValidatorFactory::createJsModel()
     */
    public function testCreateJsModel()
    {
        $factory = $this->getMock(
            'Fp\JsFormValidatorBundle\Factory\JsFormValidatorFactory',
            array('getPreparedConfig', 'processChildren', 'getEntityMetadata', 'getMappingValidationData', 'getElementValidationData', 'getTransformersList'),
            array(),
            '',
            false
        );

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
        $this->assertEquals('entity_metadata', $model->getChildren());

        // Check with child element and Class metadata
        $form  = $formFactory->create(new TestForm())->get('name');
        $model = $factory->createJsModel($form, $metadata, array());
        /** @var ClassMetadata $data */
        $data = $model->getChildren();
        $this->assertEquals('Fp\JsFormValidatorBundle\Tests\Fixtures\Entity', $data->getClassName());

        // Check with child element and not Class metadata
        $model = $factory->createJsModel($form, $metadata->getMemberMetadatas('name'), array());
        $this->assertNull($model->getChildren());
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
        $this->assertEquals(array('a', 'b'), $result);
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
        $this->assertEquals($expected, $this->callNoPublicMethod($factory, 'getPreparedConfig', array()));
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
            'translation_domain' => 'validation',
            'routing' => array(
                'check_unique_entity' => 'fp_js_form_validator.check_unique_entity'
            )
        );

        $this->assertEquals($defaultConfig, $factory->getConfig());
    }
}
