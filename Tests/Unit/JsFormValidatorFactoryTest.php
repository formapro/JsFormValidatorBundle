<?php

namespace Fp\JsFormValidatorBundle\Tests\Unit;

use Fp\JsFormValidatorBundle\Factory\JsFormValidatorFactory;
use Fp\JsFormValidatorBundle\Form\Extension\FormExtension;
use Fp\JsFormValidatorBundle\Form\Constraint\UniqueEntity as JsUniqueEntity;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity as SymfonyUniqueEntity;
use Symfony\Component\Form\ChoiceList\ArrayChoiceList;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Forms;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class JsFormValidatorFactoryTest extends TestCase
{
    public function testCreatesModelFromModernSymfonyForm()
    {
        $validator = Validation::createValidator();
        $router = $this->createMock(UrlGeneratorInterface::class);
        $router
            ->method('generate')
            ->with('fp_js_form_validator.check_unique_entity')
            ->willReturn('/fp_js_form_validator/check_unique_entity')
        ;

        $factory = new JsFormValidatorFactory(
            $validator,
            new IdentityTranslator(),
            $router,
            array(
                'js_validation' => true,
                'routing' => array(
                    'check_unique_entity' => 'fp_js_form_validator.check_unique_entity',
                ),
            ),
            'validators'
        );

        $formFactory = Forms::createFormFactoryBuilder()
            ->addExtension(new ValidatorExtension($validator))
            ->addTypeExtension(new FormExtension($factory))
            ->getFormFactory()
        ;

        $form = $formFactory
            ->createBuilder(FormType::class, null, array('validation_groups' => array('Default')))
            ->add('name', TextType::class, array('constraints' => array(new NotBlank())))
            ->getForm()
        ;

        $model = $factory->createJsModel($form);
        $config = $factory->createJsConfigModel();

        $this->assertSame('form', $model->id);
        $this->assertArrayHasKey('name', $model->children);
        $this->assertSame(TextType::class, $model->children['name']->type);
        $this->assertArrayHasKey(NotBlank::class, $model->children['name']->data['form']['constraints']);
        $this->assertSame(
            '/fp_js_form_validator/check_unique_entity',
            $config->routing['check_unique_entity']
        );
    }

    public function testUniqueEntityConstraintIncludesBoundEntityId()
    {
        $validator = Validation::createValidator();
        $router = $this->createMock(UrlGeneratorInterface::class);
        $factory = new JsFormValidatorFactory(
            $validator,
            new IdentityTranslator(),
            $router,
            array('js_validation' => true),
            'validators'
        );

        $formFactory = Forms::createFormFactoryBuilder()
            ->addExtension(new ValidatorExtension($validator))
            ->addTypeExtension(new FormExtension($factory))
            ->getFormFactory()
        ;

        $form = $formFactory
            ->createBuilder(
                FormType::class,
                new UniqueEntityUser(15, 'john@example.com'),
                array(
                    'data_class' => UniqueEntityUser::class,
                    'constraints' => array(new SymfonyUniqueEntity(fields: array('email'))),
                )
            )
            ->add('email', TextType::class)
            ->getForm()
        ;

        $model = $factory->createJsModel($form);
        $constraints = $model->data['form']['constraints'][JsUniqueEntity::class];

        $this->assertCount(1, $constraints);
        $this->assertSame(15, $constraints[0]->entityId);
        $this->assertSame(UniqueEntityUser::class, $constraints[0]->entityName);
    }

    public function testConfigModelKeepsMissingRoutesAsNull()
    {
        $router = $this->createMock(UrlGeneratorInterface::class);
        $router
            ->method('generate')
            ->willReturnCallback(static function ($route) {
                if ('missing_route' === $route) {
                    throw new \RuntimeException('Route not found.');
                }

                return '/' . $route;
            })
        ;

        $factory = $this->createFactory(null, $router, array(
            'js_validation' => true,
            'routing' => array(
                'existing' => 'existing_route',
                'missing' => 'missing_route',
            ),
        ));

        $config = $factory->createJsConfigModel();

        $this->assertSame('/existing_route', $config->routing['existing']);
        $this->assertNull($config->routing['missing']);
        $this->assertSame(
            '<script type="text/javascript">FpJsFormValidator.config = {\'routing\':{\'existing\':\'/existing_route\',\'missing\':null}};</script>',
            $factory->getJsConfigString()
        );
        $this->assertSame(
            array(
                'js_validation' => true,
                'routing' => array(
                    'existing' => 'existing_route',
                    'missing' => 'missing_route',
                ),
            ),
            $factory->getConfig()
        );
        $this->assertNull($factory->getConfig('unknown'));
    }

    public function testQueueCanBeFilteredAndProcessed()
    {
        $factory = $this->createFactory();
        $formFactory = $this->createFormFactory($factory);
        $form = $formFactory
            ->createNamedBuilder('profile', FormType::class)
            ->add('name', TextType::class)
            ->add('_token', HiddenType::class)
            ->getForm()
        ;
        $entry = $formFactory
            ->createNamedBuilder('collection_entry', TextType::class, null, array('block_name' => 'entry'))
            ->getForm()
        ;

        $factory->addToQueue($form);
        $factory->addToQueue($form->get('name'));
        $factory->addToQueue($form->get('_token'));
        $factory->addToQueue($entry);

        $this->assertTrue($factory->inQueue($form));
        $this->assertArrayHasKey('profile', $factory->getQueue());

        $factory->siftQueue();
        $this->assertSame(array('profile'), array_keys($factory->getQueue()));

        $models = $factory->processQueue();

        $this->assertCount(1, $models);
        $this->assertSame('profile', $models[0]->id);
        $this->assertSame(array(), $factory->getQueue());
    }

    public function testReturnsValidatorJavascriptForQueuedForm()
    {
        $factory = $this->createFactory();
        $formFactory = $this->createFormFactory($factory);
        $formFactory
            ->createNamedBuilder('profile', FormType::class)
            ->add('name', TextType::class, array('constraints' => array(new NotBlank())))
            ->getForm()
        ;

        $javascript = $factory->getJsValidatorString('profile', false);

        $this->assertStringContainsString('FpJsFormValidator.addModel({\'id\':\'profile\'', $javascript);
        $this->assertStringEndsWith(', false);', $javascript);
        $this->assertSame(array(), $factory->getQueue());
    }

    public function testThrowsWhenRequestedQueuedFormDoesNotExist()
    {
        $factory = $this->createFactory();
        $formFactory = $this->createFormFactory($factory);
        $formFactory->createNamedBuilder('profile', FormType::class)->getForm();

        $this->expectException(\Fp\JsFormValidatorBundle\Exception\UndefinedFormException::class);
        $this->expectExceptionMessage("Form 'missing' was not found. Existing forms: profile");

        $factory->getJsValidatorString('missing');
    }

    public function testReturnsEmptyJavascriptForDisabledForm()
    {
        $factory = $this->createFactory();
        $formFactory = $this->createFormFactory($factory);
        $form = $formFactory
            ->createNamedBuilder('profile', FormType::class, null, array('js_validation' => false))
            ->getForm()
        ;

        $factory->addToQueue($form);

        $this->assertNull($factory->createJsModel($form));
        $this->assertSame('', $factory->getJsValidatorString());
    }

    public function testValidationGroupsClosureIsSerializedAsFormId()
    {
        $factory = $this->createFactory();
        $formFactory = $this->createFormFactory($factory);
        $form = $formFactory
            ->createNamedBuilder('profile', FormType::class, null, array(
                'validation_groups' => static function () {
                    return array('IgnoredAtRuntime');
                },
            ))
            ->add('name', TextType::class, array('constraints' => array(new NotBlank())))
            ->getForm()
        ;

        $model = $factory->createJsModel($form);

        $this->assertSame('profile', $model->children['name']->data['form']['groups']);
    }

    public function testParentAndOwnValidationMetadataAreSerialized()
    {
        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator()
        ;
        $factory = $this->createFactory($validator);
        $formFactory = $this->createFormFactory($factory, $validator);
        $form = $formFactory
            ->createNamedBuilder('profile', FormType::class, new MetadataUser(), array(
                'data_class' => MetadataUser::class,
            ))
            ->add('name', TextType::class)
            ->getForm()
        ;

        $model = $factory->createJsModel($form);

        $this->assertArrayHasKey(Assert\NotBlank::class, $model->children['name']->data['parent']['constraints']);
        $this->assertSame(array('Default'), $model->children['name']->data['parent']['groups']);
        $this->assertArrayHasKey('isActive', $model->data['entity']['getters']);
        $this->assertArrayHasKey(Assert\IsTrue::class, $model->data['entity']['getters']['isActive']);
    }

    public function testExpandedChoicesExposeBooleanArrayTransformers()
    {
        $factory = $this->createFactory();
        $formFactory = $this->createFormFactory($factory);
        $form = $formFactory
            ->createNamedBuilder('profile', FormType::class)
            ->add('status', ChoiceType::class, array(
                'choices' => array('Enabled' => 'enabled', 'Disabled' => 'disabled'),
                'expanded' => true,
                'multiple' => false,
            ))
            ->add('tags', ChoiceType::class, array(
                'choices' => array('Public' => 'public', 'Featured' => 'featured'),
                'expanded' => true,
                'multiple' => true,
            ))
            ->getForm()
        ;

        $model = $factory->createJsModel($form);

        $this->assertSame(
            'Symfony\Component\Form\Extension\Core\DataTransformer\ChoiceToBooleanArrayTransformer',
            $model->children['status']->transformers[0]['name']
        );
        $this->assertSame(array('enabled', 'disabled'), $model->children['status']->transformers[0]['choiceList']);
        $this->assertSame(
            'Symfony\Component\Form\Extension\Core\DataTransformer\ChoicesToBooleanArrayTransformer',
            $model->children['tags']->transformers[0]['name']
        );
        $this->assertSame(array('public', 'featured'), $model->children['tags']->transformers[0]['choiceList']);
    }

    public function testProtectedTransformerAndMergeHelpers()
    {
        $factory = new TestableJsFormValidatorFactory(
            Validation::createValidator(),
            new IdentityTranslator(),
            $this->createMock(UrlGeneratorInterface::class),
            array('js_validation' => true),
            'validators'
        );

        $parsed = $factory->exposedParseTransformers(array(new TransformerFixture()));

        $this->assertSame(TransformerFixture::class, $parsed[0]['name']);
        $this->assertSame('scalar-value', $parsed[0]['scalarValue']);
        $this->assertSame(array('left' => 'right'), $parsed[0]['arrayValue']);
        $this->assertNull($parsed[0]['objectValue']);
        $this->assertSame(array('first', 'second'), $parsed[0]['choiceList']);
        $this->assertSame(NestedTransformerFixture::class, $parsed[0]['transformers'][0]['name']);
        $this->assertSame('nested-value', $parsed[0]['transformers'][0]['nestedValue']);

        $this->assertSame(
            array(
                'nested' => array('left' => 1, 'right' => 2),
                'list' => array('a', 'b'),
                'scalar' => 'new',
                'fresh' => array('value'),
            ),
            $factory->exposedMergeDataRecursive(
                array(
                    'nested' => array('left' => 1),
                    'list' => array('a'),
                    'scalar' => 'old',
                ),
                array(
                    'nested' => array('right' => 2),
                    'list' => array('b'),
                    'scalar' => 'new',
                    'fresh' => array('value'),
                )
            )
        );
    }

    private function createFactory(
        ?ValidatorInterface $validator = null,
        ?UrlGeneratorInterface $router = null,
        array $config = array('js_validation' => true)
    ) {
        if (!$router) {
            $router = $this->createMock(UrlGeneratorInterface::class);
            $router
                ->method('generate')
                ->willReturn('/generated-route')
            ;
        }

        return new JsFormValidatorFactory(
            $validator ?: Validation::createValidator(),
            new IdentityTranslator(),
            $router,
            $config,
            'validators'
        );
    }

    private function createFormFactory(JsFormValidatorFactory $factory, ?ValidatorInterface $validator = null)
    {
        $validator = $validator ?: Validation::createValidator();

        return Forms::createFormFactoryBuilder()
            ->addExtension(new ValidatorExtension($validator))
            ->addTypeExtension(new FormExtension($factory))
            ->getFormFactory()
        ;
    }
}

class IdentityTranslator implements TranslatorInterface
{
    public function trans(
        string $id,
        array $parameters = array(),
        ?string $domain = null,
        ?string $locale = null
    ): string {
        return strtr($id, $parameters);
    }

    public function getLocale(): string
    {
        return 'en';
    }
}

class UniqueEntityUser
{
    public $email;

    private $id;

    public function __construct($id, $email)
    {
        $this->id = $id;
        $this->email = $email;
    }

    public function getId()
    {
        return $this->id;
    }
}

class MetadataUser
{
    #[Assert\NotBlank(message: 'Name is required.')]
    public $name = '';

    #[Assert\IsTrue(message: 'User must be active.')]
    public function isActive()
    {
        return true;
    }
}

class TestableJsFormValidatorFactory extends JsFormValidatorFactory
{
    public function exposedParseTransformers(array $transformers)
    {
        return $this->parseTransformers($transformers);
    }

    public function exposedMergeDataRecursive(array $array1, array $array2)
    {
        return $this->mergeDataRecursive($array1, $array2);
    }
}

class TransformerFixture implements DataTransformerInterface
{
    private $scalarValue = 'scalar-value';

    private $arrayValue = array('left' => 'right');

    private $objectValue;

    private $choiceList;

    private $transformers;

    public function __construct()
    {
        $this->objectValue = new \stdClass();
        $this->choiceList = new ArrayChoiceList(array('First' => 'first', 'Second' => 'second'));
        $this->transformers = array(new NestedTransformerFixture());
    }

    public function transform(mixed $value): mixed
    {
        return array(
            $value,
            $this->scalarValue,
            $this->arrayValue,
            $this->objectValue,
            $this->choiceList,
            $this->transformers,
        );
    }

    public function reverseTransform(mixed $value): mixed
    {
        return $value;
    }
}

class NestedTransformerFixture implements DataTransformerInterface
{
    private $nestedValue = 'nested-value';

    public function transform(mixed $value): mixed
    {
        return array($value, $this->nestedValue);
    }

    public function reverseTransform(mixed $value): mixed
    {
        return $value;
    }
}
