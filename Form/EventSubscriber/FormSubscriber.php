<?php
namespace Fp\JsFormValidatorBundle\Form\EventSubscriber;

use Fp\JsFormValidatorBundle\Factory\JsFormValidatorFactory;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * Class FormSubscriber
 *
 * @package Fp\JsFormValidatorBundle\Form\EventSubscriber
 */
class FormSubscriber implements EventSubscriberInterface
{
    /**
     * @var JsFormValidatorFactory
     */
    protected $factory;

    /**
     * @param JsFormValidatorFactory $factory
     */
    public function __construct(JsFormValidatorFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(FormEvents::POST_SET_DATA => array('generateJsValidator', -10));
    }

    /**
     * @param FormEvent $event
     */
    public function generateJsValidator(FormEvent $event)
    {
        /** @var Form $form */
        $form = $event->getForm();
        $this->factory->addToQueue($form);
    }
} 