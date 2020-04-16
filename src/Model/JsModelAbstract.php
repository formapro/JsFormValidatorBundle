<?php

namespace Fp\JsFormValidatorBundle\Model;

/**
 * All the models inherited from this class converted to a similar Javascript model by printing them as a string
 *
 * Class PhpToJsModel
 *
 * @package Fp\JsFormValidatorBundle\Model
 */
abstract class JsModelAbstract
{
    /**
     * This function converts the model to the related JavaScript model
     *
     * @return string
     */
    public function toJsString()
    {
        return self::phpValueToJs($this->toArray());
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->toJsString();
    }

    /**
     * Convert php value to the Javascript formatted string
     *
     * @param mixed $value
     *
     * @return string
     */
    public static function phpValueToJs($value)
    {
        // For object which has own __toString method
        if ($value instanceof JsModelAbstract) {
            return $value->toJsString();
        }
        // For object which has own __toString method
        elseif (is_object($value) && method_exists($value, '__toString')) {
            return self::phpValueToJs($value->__toString());
        }
        // For an object or associative array
        elseif (is_object($value) || (is_array($value) && array_values($value) !== $value)) {
            $jsObject = array();
            foreach ($value as $paramName => $paramValue) {
                $paramName = addcslashes($paramName, '\'\\');
                $jsObject[] = "'$paramName':" . self::phpValueToJs($paramValue);
            }

            return sprintf('{%1$s}', implode(',', $jsObject));
        }
        // For a sequential array
        elseif (is_array($value)) {
            $jsArray = array();
            foreach ($value as $item) {
                $jsArray[] = self::phpValueToJs($item);
            }

            return sprintf('[%1$s]', implode(',', $jsArray));
        }
        // For string
        elseif (is_string($value)) {
            $value = addcslashes($value, '\'\\');

            return "'$value'";
        }
        // For boolean
        elseif (is_bool($value)) {
            return true === $value ? 'true' : 'false';
        }
        // For numbers
        elseif (is_numeric($value)) {
            return $value;
        }
        // For null
        elseif (is_null($value)) {
            return 'null';
        }
        // Otherwise
        else {
            return 'undefined';
        }
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $result = array();
        foreach ($this as $key => $value) {
            $result[$key] = $value;
        }

        return $result;
    }
} 
