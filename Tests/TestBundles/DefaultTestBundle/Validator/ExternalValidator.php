<?php
namespace Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Validator;

use Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Entity\BasicConstraintsEntity;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class ExternalValidator {
    /**
     * @param BasicConstraintsEntity    $object
     * @param ExecutionContextInterface $context
     */
    public static function validateStaticCallback($object, ExecutionContextInterface $context)
    {
        if (!$object->isValid) {
            $context->buildViolation('static_callback_email_' . $object->getEmail())->atPath('email');
        }
    }

    /**
     * @param BasicConstraintsEntity    $object
     * @param ExecutionContextInterface $context
     */
    public static function validateDirectStaticCallback($object, ExecutionContextInterface $context)
    {
        if (!$object->isValid) {
            $context->buildViolation('direct_static_callback_email_' . $object->getEmail())->atPath('email');
        }
    }
} 