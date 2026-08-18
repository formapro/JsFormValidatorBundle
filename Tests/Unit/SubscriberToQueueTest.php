<?php

namespace Fp\JsFormValidatorBundle\Tests\Unit;

use Fp\JsFormValidatorBundle\Factory\JsFormValidatorFactory;
use Fp\JsFormValidatorBundle\Form\Extension\FormExtension;
use Fp\JsFormValidatorBundle\Form\Subscriber\SubscriberToQueue;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\Forms;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Contracts\Translation\TranslatorInterface;

class SubscriberToQueueTest extends TestCase
{
    public function testAddToQueueWhenGlobalDisabledButLocalExplicitlyEnabled()
    {
        $factory = $this->createFactory(array('js_validation' => false));
        $formFactory = $this->createFormFactory($factory);
        $subscriber = new SubscriberToQueue($factory);

        $form = $formFactory
            ->createNamedBuilder('test_form', FormType::class, null, array('js_validation' => true))
            ->getForm()
        ;

        $subscriber->onFormSetData(new FormEvent($form, null));

        $this->assertTrue($factory->inQueue($form));
    }

    public function testDoesNotAddToQueueWhenGlobalDisabledAndLocalNotSet()
    {
        $factory = $this->createFactory(array('js_validation' => false));
        $formFactory = $this->createFormFactory($factory);
        $subscriber = new SubscriberToQueue($factory);

        // Form with no explicit js_validation option (defaults to null)
        $form = $formFactory
            ->createNamedBuilder('test_form', FormType::class)
            ->getForm()
        ;

        $subscriber->onFormSetData(new FormEvent($form, null));

        $this->assertFalse($factory->inQueue($form));
    }

    public function testAddToQueueWhenGlobalEnabledAndLocalNotSet()
    {
        $factory = $this->createFactory(array('js_validation' => true));
        $formFactory = $this->createFormFactory($factory);
        $subscriber = new SubscriberToQueue($factory);

        // Form with no explicit js_validation option (defaults to null, inherits global)
        $form = $formFactory
            ->createNamedBuilder('test_form', FormType::class)
            ->getForm()
        ;

        $subscriber->onFormSetData(new FormEvent($form, null));

        $this->assertTrue($factory->inQueue($form));
    }

    public function testDoesNotAddToQueueWhenLocalExplicitlyDisabled()
    {
        $factory = $this->createFactory(array('js_validation' => true));
        $formFactory = $this->createFormFactory($factory);
        $subscriber = new SubscriberToQueue($factory);

        $form = $formFactory
            ->createNamedBuilder('test_form', FormType::class, null, array('js_validation' => false))
            ->getForm()
        ;

        $subscriber->onFormSetData(new FormEvent($form, null));

        $this->assertFalse($factory->inQueue($form));
    }

    private function createFactory(array $config = array())
    {
        return new JsFormValidatorFactory(
            Validation::createValidator(),
            $this->createStub(TranslatorInterface::class),
            $this->createStub(UrlGeneratorInterface::class),
            $config,
            'validators'
        );
    }

    private function createFormFactory(JsFormValidatorFactory $factory)
    {
        return Forms::createFormFactoryBuilder()
            ->addExtension(new ValidatorExtension(Validation::createValidator()))
            ->addTypeExtension(new FormExtension($factory))
            ->getFormFactory()
        ;
    }
}
