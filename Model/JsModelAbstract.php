<?php
/**
 * Created by PhpStorm.
 * User: Yury Maltsev
 * Email: dev.ymalcev@gmail.com
 * Date: 11/19/13
 * Time: 1:12 PM
 */

namespace Fp\JsFormValidatorBundle\Model;

/**
 * Class PhpToJsModel
 * @package Fp\JsFormValidatorBundle\Model
 */
abstract class JsModelAbstract {
    const OUTPUT_FORMAT_JAVASCRIPT = 'js';
    const OUTPUT_FORMAT_JSON       = 'json';

    protected $outputFormat = self::OUTPUT_FORMAT_JAVASCRIPT;

    public function __toString()
    {
        switch ($this->outputFormat) {
            case self::OUTPUT_FORMAT_JAVASCRIPT:
                return $this->phpValueToJs($this->toArray());
                break;
            default:
                return @json_encode($this->toArray());
                break;
        }
    }

    /**
     * @param $value
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
            $value = addcslashes($value, '\'');
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
        elseif (null === $value) {
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
     * @return PhpToJsModel
     */
    public function setOutputFormat($outputFormat)
    {
        $this->outputFormat = $outputFormat;

        return $this;
    }
} 