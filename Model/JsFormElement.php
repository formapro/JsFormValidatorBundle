<?php
/**
 * Created by PhpStorm.
 * User: Yury Maltsev
 * Email: dev.ymalcev@gmail.com
 * Date: 11/6/13
 * Time: 5:50 PM
 */

namespace Fp\JsFormValidatorBundle\Model;

use Symfony\Component\Form\ResolvedFormTypeInterface;

class JsFormElement extends JsModelAbstract
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $dataClass = null;

    /**
     * @var string
     */
    protected $type = null;

    /**
     * @var array
     */
    protected $transformers = array();

    /**
     * @var JsValidationData[]
     */
    protected $validationData = array();

    /**
     * @var bool
     */
    protected $cascadeValidation = true;

    /**
     * @var array
     */
    protected $jsEvents = array();

    /**
     * @var JsFormElement[]
     */
    protected $children = array();

    /**
     * @var array
     */
    protected $config = array();

    /**
     * @param string $id
     * @param string $name
     */
    function __construct($id, $name)
    {
        $this->id   = $id;
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('new FpJsFormElement(%1$s)', parent::__toString());
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return array(
            'id'                => $this->getId(),
            'name'              => $this->getName(),
            'dataClass'         => $this->getDataClass(),
            'type'              => $this->getType(),
            'validationData'    => array_values($this->getValidationData()),
            'transformers'      => $this->getTransformers(),
            'cascadeValidation' => $this->getCascadeValidation(),
            'events'            => $this->getJsEvents(),
            'children'          => $this->getChildren(),
            'config'            => $this->getConfig(),
        );
    }

    /**
     * Get Id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set transformers
     *
     * @param array $transformers
     *
     * @return JsFormElement
     */
    public function setTransformers($transformers)
    {
        $this->transformers = $transformers;

        return $this;
    }

    /**
     * Get Transformers
     *
     * @return array
     */
    public function getTransformers()
    {
        return $this->transformers;
    }

    /**
     * Get validationData
     *
     * @return \Fp\JsFormValidatorBundle\Model\JsValidationData[]
     */
    public function getValidationData()
    {
        return $this->validationData;
    }

    /**
     * Set validationData
     *
     * @param \Fp\JsFormValidatorBundle\Model\JsValidationData|array $validationData
     *
     * @return JsFormElement
     */
    public function addValidationData($validationData)
    {
        if ($validationData instanceof JsValidationData) {
            $this->validationData[spl_object_hash($validationData)] = $validationData;
        } elseif (is_array($validationData)) {
            foreach ($validationData as $value) {
                $this->addValidationData($value);
            }
        }

        return $this;
    }

    /**
     * @param array $events
     *
     * @return $this
     */
    public function setJsEvents($events)
    {
        $this->jsEvents = $events;

        return $this;
    }

    /**
     * Get JsEvents
     *
     * @return array
     */
    public function getJsEvents()
    {
        return $this->jsEvents;
    }

    /**
     * Set children
     *
     * @param JsFormElement[] $children
     *
     * @return JsFormElement
     */
    public function setChildren($children)
    {
        $this->children = $children;

        return $this;
    }

    /**
     * Get Children
     *
     * @return JsFormElement[]
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @param $name
     *
     * @return JsFormElement|null
     */
    public function getChild($name)
    {
        if (isset($this->children[$name])) {
            return $this->children[$name];
        } else {
            return null;
        }
    }

    /**
     * Get CascadeValidation
     *
     * @return boolean
     */
    public function getCascadeValidation()
    {
        return $this->cascadeValidation;
    }

    /**
     * Set cascadeValidation
     *
     * @param boolean $cascadeValidation
     *
     * @return JsFormElement
     */
    public function setCascadeValidation($cascadeValidation)
    {
        $this->cascadeValidation = $cascadeValidation;

        return $this;
    }

    /**
     * Set dataClass
     *
     * @param string $dataClass
     *
     * @return JsFormElement
     */
    public function setDataClass($dataClass)
    {
        $this->dataClass = $dataClass;

        return $this;
    }

    /**
     * Get DataClass
     *
     * @return string
     */
    public function getDataClass()
    {
        return $this->dataClass;
    }

    /**
     * Get Type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set type
     *
     * @param ResolvedFormTypeInterface $type
     *
     * @return JsFormElement
     */
    public function setType($type)
    {
        $this->type = $type->getInnerType()->getName();

        return $this;
    }

    /**
     * Get Config
     *
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Set config
     *
     * @param array $config
     *
     * @return JsFormElement
     */
    public function setConfig($config)
    {
        $this->config = $config;

        return $this;
    }

    /**
     * Get Name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
} 