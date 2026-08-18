<?php

namespace Fp\JsFormValidatorBundle\Tests\Unit;

use Fp\JsFormValidatorBundle\Factory\JsFormValidatorFactory;
use Fp\JsFormValidatorBundle\Form\Extension\FormExtension;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Forms;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

trait FactoryTestTrait
{
    private function createFactory(
        ?ValidatorInterface $validator = null,
        ?UrlGeneratorInterface $router = null,
        array $config = array('js_validation' => true)
    ): JsFormValidatorFactory {
        if (!$router) {
            $router = $this->createStub(UrlGeneratorInterface::class);
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
    public function trans(string $id, array $parameters = array(), ?string $domain = null, ?string $locale = null): string
    {
        return strtr($id, $parameters);
    }

    public function getLocale(): string
    {
        return 'en';
    }
}
