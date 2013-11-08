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
     * @var string
     */
    public $parentFormId;

    /**
     * @var array
     */
    public $transformers = array();

    /**
     * @var array
     */
    public $events = array();

    /**
     * @var array
     */
    public $getters = array();

    /**
     * @var array
     */
    public $constraints = array();

    /**
     * @var array
     */
    public $children = array();

    private $jsClassName = 'FpJsFormType';

    /**
     * @param $parentFormId
     */
    function __construct($parentFormId) {
        $this->parentFormId                = $parentFormId;
        $this->events[$this->parentFormId] = array('submit');
    }

    /**
     * @return string
     */
    public function createJsObject()
    {
        return sprintf('new %1$s(JSON.parse(\'%2$s\')); ', $this->jsClassName, json_encode($this));
    }

    /**
     * @param $elementId
     * @param array $events
     */
    public function addEvents($elementId, array $events)
    {
        foreach ($events as $event) {
            $this->events[$elementId][] = $event;
        }
    }
} 