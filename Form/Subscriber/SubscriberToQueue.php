<?php
namespace Fp\JsFormValidatorBundle\Form\Subscriber;

use Fp\JsFormValidatorBundle\Factory\JsFormValidatorFactory;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

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
        $form = $event->getForm();

        // Add only parent forms which are not disabled
        if (
            !$form->getParent() &&
            $this->isEnabled($form) &&
            'form' == $form->getConfig()->getType()->getInnerType()->getName()
        ) {
            $this->factory->addToQueue($form);
        }
    }

    /**
     * Checks if one of element's parents already has enabled/disabled
     *
     * @param Form|FormInterface $form
     *
     * @return bool
     */
    protected function isEnabled(Form $form)
    {
        if (
            false === $this->factory->getConfig('js_validation') ||
            false === $form->getConfig()->getOption('js_validation')
        ) {
            return false;
        } elseif ($form->getParent()) {
            return $this->isEnabled($form->getParent());
        } else {
            return true;
        }
    }
} 