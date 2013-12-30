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
    const OUTPUT_FORMAT_JAVASCRIPT = 'js';
    const OUTPUT_FORMAT_JSON       = 'json';

    protected $outputFormat = self::OUTPUT_FORMAT_JAVASCRIPT;

    /**
     * This function converts the model to the related JavaScript model
     *
     * @return string
     */
    public function __toString()
    {
        switch ($this->outputFormat) {
            case self::OUTPUT_FORMAT_JAVASCRIPT:
                return $this->phpValueToJs($this->toArray());
                break;
            default:
                return json_encode($this->toArray());
                break;
        }
    }

    /**
     * Convert php value to the Javascript formatted string
     *
     * @param mixed $value
     *
     * @return string
     */
    protected function phpValueToJs($value)
    {
        // For object which has own __toString method
        if (is_object($value) && method_exists($value, '__toString')) {
            return $value->__toString();
        }
        // For an object or associative array
        elseif (is_object($value) || (is_array($value) && array_values($value) !== $value)) {
            $jsObject = array();
            foreach ($value as $paramName => $paramValue) {
                $jsObject[] = "'$paramName':" . $this->phpValueToJs($paramValue);
            }

            return sprintf('{%1$s}', implode($jsObject, ','));
        }
        // For a sequential array
        elseif (is_array($value)) {
            $jsArray = array();
            foreach ($value as $item) {
                $jsArray[] = $this->phpValueToJs($item);
            }

            return sprintf('[%1$s]', implode($jsArray, ','));
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
     * @codeCoverageIgnore
     * @return array
     */
    abstract public function toArray();

    /**
     * Get OutputFormat
     *
     * @return string
     */
    public function getOutputFormat()
    {
        return $this->outputFormat;
    }

    /**
     * Set outputFormat
     *
     * @param string $outputFormat
     *
     * @return JsModelAbstract
     */
    public function setOutputFormat($outputFormat)
    {
        $this->outputFormat = $outputFormat;

        return $this;
    }
} 