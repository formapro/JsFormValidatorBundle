<?php
namespace Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Validator;

use Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Entity\BasicConstraintsEntity;
use Symfony\Component\Validator\ExecutionContextInterface;

class ExternalValidator {
    /**
     * @param BasicConstraintsEntity    $object
     * @param ExecutionContextInterface $context
     */
    public static function validateStaticCallback($object, ExecutionContextInterface $context)
    {
        if (!$object->isValid) {
            $context->addViolationAt('email', 'static_callback_email_' . $object->getEmail(), array(), null);
        }
    }

    /**
     * @param BasicConstraintsEntity    $object
     * @param ExecutionContextInterface $context
     */
    public static function validateDirectStaticCallback($object, ExecutionContextInterface $context)
    {
        if (!$object->isValid) {
            $context->addViolationAt('email', 'direct_static_callback_email_' . $object->getEmail(), array(), null);
        }
    }
} 