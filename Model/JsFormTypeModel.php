<?php
/**
 * Created by PhpStorm.
 * User: Yury Maltsev
 * Email: dev.ymalcev@gmail.com
 * Date: 11/6/13
 * Time: 5:50 PM
 */

namespace Fp\JsFormValidatorBundle\Model;


class JsFormTypeModel {
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
    public $fullName;

    /**
     * @var array
     */
    public $transformers = [];

    /**
     * @var array
     */
    public $events = [];

    /**
     * @var array
     */
    public $getters = [];

    /**
     * @var array
     */
    public $constraints = [];

    /**
     * @var array
     */
    public $children = [];

    private $jsClassName = 'FpJsFormType';

    public function createJsObject()
    {
        return sprintf('new %1$s(JSON.parse(\'%2$s\')); ', $this->jsClassName, json_encode($this));
    }
} 