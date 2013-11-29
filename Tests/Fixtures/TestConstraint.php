<?php
/**
 * Created by PhpStorm.
 * User: Yury Maltsev
 * Email: dev.ymalcev@gmail.com
 * Date: 11/12/13
 * Time: 12:32 PM
 */

namespace Fp\JsFormValidatorBundle\Tests\Fixtures;


use Symfony\Component\Validator\Constraint;

class TestConstraint extends Constraint {
    public $errorMessage = 'test_custom_constraint.error_message';
    public $messageError = 'test_custom_constraint.message_error';
    public $someMessageError = 'test_custom_constraint.some_message_error';
    public $value = 'exact_value';
} 