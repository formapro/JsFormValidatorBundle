<?php

namespace Fp\JsFormValidatorBundle\Tests\Unit;

use Fp\JsFormValidatorBundle\Factory\JsFormValidatorFactory;
use Fp\JsFormValidatorBundle\Form\Extension\FormExtension;
use Fp\JsFormValidatorBundle\Form\Constraint\UniqueEntity as JsUniqueEntity;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity as SymfonyUniqueEntity;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Forms;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validation;
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
