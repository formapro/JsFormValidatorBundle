<?php

namespace Fp\JsFormValidatorBundle\Tests\Fixtures;

/**
 * Class SimpleToStringObject
 *
 * @package Fp\JsFormValidatorBundle\Tests\Fixtures
 */
class SimpleToStringObject
{
    /**
     * @return string
     */
    public function __toString()
    {
        return "'toStringName'";
    }
} 