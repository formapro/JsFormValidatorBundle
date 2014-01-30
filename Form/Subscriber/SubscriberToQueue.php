<?php
namespace Fp\JsFormValidatorBundle\Form\Subscriber;

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
class SubscriberToQueue implements EventSubscriberInterface
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
        return array(FormEvents::POST_SET_DATA => array('onFormSetData', -10));
    }

    /**
     * @param FormEvent $event
     */
    public function onFormSetData(FormEvent $event)
    {
        /** @var Form $form */
        $form         = $event->getForm();
        $globalSwitch = $this->factory->getConfig('js_validation');
        $localSwitch  = $form->getConfig()->getOption('js_validation');
        $isForm       = 'form' == $form->getConfig()->getType()->getInnerType()->getName();

        // Add only parent forms which are not disabled
        if (!$form->getParent() && $globalSwitch && $localSwitch && $isForm) {
            $this->factory->addToQueue($form);
        }
    }
} 