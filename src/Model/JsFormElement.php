<?php

namespace Fp\JsFormValidatorBundle\Model;

/**
 * This is the main model that describes each of the form elements
 *
 * Class JsFormElement
 *
 * @package Fp\JsFormValidatorBundle\Model
 */
class JsFormElement extends JsModelAbstract
{
    /**
     * @var string
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $type;

    /**
     * @var string
     */
    public $invalidMessage;

    /**
     * @var bool
     */
    public $bubbling = false;

    /**
     * @var array
     */
    public $data = array();

    /**
     * @var array
     */
    public $transformers = array();

    /**
     * @var JsFormElement[]
     */
    public $children = array();

    /**
     * @var JsFormElement
     */
    public $prototype = null;
} 