<?php

namespace Fp\JsFormValidatorBundle\Tests\Fixtures;

use Symfony\Component\Validator\Constraint;

/**
 * Class TestConstraint
 *
 * @package Fp\JsFormValidatorBundle\Tests\Fixtures
 */
class TestConstraint extends Constraint
{
    /**
     * @var string
     */
    public $errorMessage = 'test_custom_constraint.error_message';
    /**
     * @var string
     */
    public $messageError = 'test_custom_constraint.message_error';
    /**
     * @var string
     */
    public $someMessageError = 'test_custom_constraint.some_message_error';
    /**
     * @var string
     */
    public $value = 'exact_value';
}
