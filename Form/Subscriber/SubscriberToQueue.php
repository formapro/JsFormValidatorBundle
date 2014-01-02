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
        return array(FormEvents::POST_SET_DATA => array('onFormEvent', -10));
    }

    /**
     * @param FormEvent $event
     */
    public function onFormEvent(FormEvent $event)
    {
        /** @var Form $form */
        $form = $event->getForm();

        if ($this->formShouldBeProcessed($form)) {
            $this->factory->addToQueue($form);
        }
    }

    /**
     * @param Form $form
     *
     * @return bool
     */
    protected function formShouldBeProcessed(Form $form)
    {
        $conf = $this->factory->getConfig();
        $jsGlobal = $conf['js_validation'];
        $jsLocal = $form->getConfig()->getOption('js_validation');

        // If validation is enabled globally: add all the parent forms which are not disabled locally
        if (true === $jsGlobal && false !== $jsLocal && null === $form->getParent()) {
            return true;
        // If global option is not set and the element is enabled locally (doesn't matter this is parent or child)
        } elseif (null === $jsGlobal && true === $jsLocal) {
            // If this is enabled child and parent has been already enabled - do not need to duplicate validation
            if (null !== $form->getParent() && true === $this->getParent($form)->getConfig()->getOption('js_validation')) {
                return false;
            } else {
                return true;
            }
        } else {
            return false;
        }
    }

    /**
     * @param Form $form
     *
     * @return null|\Symfony\Component\Form\FormInterface
     */
    protected function getParent(Form $form)
    {
        if (null === $form->getParent()) {
            return $form;
        } else {
            return $this->getParent($form->getParent());
        }
    }
} 