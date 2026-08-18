<?php

namespace Fp\JsFormValidatorBundle\Tests\Unit;

use Fp\JsFormValidatorBundle\Factory\JsFormValidatorFactory;
use Fp\JsFormValidatorBundle\Form\Subscriber\SubscriberToQueue;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormEvent;
use Symfony\Contracts\Translation\TranslatorInterface;

class SubscriberToQueueTest extends TestCase
{
    use FactoryTestTrait;
    public function testAddToQueueWhenGlobalDisabledButLocalExplicitlyEnabled()
    {
        $factory = $this->createFactory(null, null, array('js_validation' => false));
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
        $factory = $this->createFactory(null, null, array('js_validation' => false));
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
        $factory = $this->createFactory(null, null, array('js_validation' => true));
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
        $factory = $this->createFactory(null, null, array('js_validation' => true));
        $formFactory = $this->createFormFactory($factory);
        $subscriber = new SubscriberToQueue($factory);

        $form = $formFactory
            ->createNamedBuilder('test_form', FormType::class, null, array('js_validation' => false))
            ->getForm()
        ;

        $subscriber->onFormSetData(new FormEvent($form, null));

        $this->assertFalse($factory->inQueue($form));
    }

    public function testChildFieldOptInAddsEntireFormToQueue()
    {
        // Global disabled, but a child field explicitly opts in
        $factory = $this->createFactory(null, null, array('js_validation' => false));
        $formFactory = $this->createFormFactory($factory);
        $subscriber = new SubscriberToQueue($factory);

        $form = $formFactory
            ->createNamedBuilder('test_form', FormType::class)
            ->add('email', FormType::class, array('js_validation' => true))
            ->getForm()
        ;

        // Trigger subscriber on the child field that opts in
        $child = $form->get('email');
        $subscriber->onFormSetData(new FormEvent($child, null));

        // The entire parent form should be in the queue
        $this->assertTrue($factory->inQueue($form));
        $this->assertArrayHasKey('test_form', $factory->getQueue());
    }
}
