<?php
namespace Fp\JsFormValidatorBundle\Factory;

use Fp\JsFormValidatorBundle\Model\JsFormElement;
use Fp\JsFormValidatorBundle\Model\JsModelAbstract;
use Fp\JsFormValidatorBundle\Model\JsValidationData;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceListInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Mapping\GetterMetadata;
use Symfony\Component\Validator\Mapping\PropertyMetadata;
use Symfony\Component\Validator\Validator;

/**
 * This factory uses to parse a form to a tree of JsFormElement's
 *
 * Class JsFormValidatorFactory
 *
 * @package Fp\JsFormValidatorBundle\Factory
 */
class JsFormValidatorFactory
{
    /**
     * @var Validator
     */
    protected $validator;

    /**
     * @var Translator
     */
    protected $translator;

    /**
     * @var array
     */
    protected $config = array();

    /**
     * @var Form[]
     */
    protected $queue = array();

    /**
     * @param Validator  $validator
     * @param Translator $translator
     * @param array      $config
     */
    public function __construct(Validator $validator, Translator $translator, $config)
    {
        $this->validator  = $validator;
        $this->translator = $translator;
        $this->config     = $config;
    }

    /**
     * Gets metadata from system using the entity class name
     *
     * @param string $className
     *
     * @return ClassMetadata
     * @codeCoverageIgnore
     */
    protected function getMetadataFor($className)
    {
        return $this->validator->getMetadataFactory()->getMetadataFor($className);
    }

    /**
     * Translate a single message
     *
     * @param string $message
     *
     * @return string
     * @codeCoverageIgnore
     */
    protected function translateMessage($message)
    {
        return $this->translator->trans($message, array(), $this->config['translation_domain']);
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
     * Add a new form to processing queue
     *
     * @param \Symfony\Component\Form\Form $form
     *
     * @return array
     */
    public function addToQueue(Form $form)
    {
        $this->queue[$form->getName()] = $form;
    }

    /**
     * @return JsFormElement[]
     */
    public function processQueue()
    {
        $result = array();

        foreach ($this->queue as $form) {
            $model = $this->createJsModel($form);
            if ($model) {
                $result[] = $model;
            }
        };

        $this->queue = array();

        return $result;
    }

    /**
     * The main function that creates nested model
     *
     * @param Form $form
     *
     * @return null|JsFormElement
     */
    public function createJsModel(Form $form)
    {
        $conf         = $form->getConfig();
        $isDisabled   = false === $conf->getOption('js_validation');
        $vData        = $this->getValidationData($form);
        $transformers = $this->parseTransformers($form->getConfig()->getViewTransformers());
        $children     = $this->processChildren($form);

        // If field is disabled or has no any validations
        if ($isDisabled || (!$vData && !$transformers && !$this->hasParentTransformers($form) && empty($children))) {
            return null;
        }

        $model                 = new JsFormElement;
        $model->id             = $this->getElementId($form);
        $model->name           = $form->getName();
        $model->type           = $conf->getType()->getInnerType()->getName();
        $model->invalidMessage = $conf->getOption('invalid_message');
        $model->transformers   = $transformers;
        $model->cascade        = $conf->getOption('cascade_validation');
        $model->data           = $vData;
        $model->children       = $children;

        // Return self id to add it as child to the parent model
        return $model;
    }

    /**
     * Create the JsFormElement for all the children of specified element
     *
     * @param null|Form $form
     *
     * @return array
     */
    protected function processChildren($form)
    {
        $result = array();
        // If this field has children - process them
        foreach ($form as $name => $child) {
            if ($this->isProcessableElement($child)) {
                $childModel = $this->createJsModel($child);
                if (null !== $childModel) {
                    $result[$name] = $childModel;
                }
            }
        }

        return $result;
    }

    /**
     * @param Form|FormInterface $form
     *
     * @return bool
     */
    protected function hasParentTransformers(Form $form)
    {
        if ($form->getConfig()->getViewTransformers()) {
            return true;
        } elseif ($form->getParent()) {
            return $this->hasParentTransformers($form->getParent());
        } else {
            return false;
        }
    }

    /**
     * Generate an Id for the element by merging the current element name
     * with all the parents names
     *
     * @param Form $form
     *
     * @return string
     */
    protected function getElementId(Form $form)
    {
        /** @var Form $parent */
        $parent = $form->getParent();
        if (null !== $parent) {
            return $this->getElementId($parent) . '_' . $form->getName();
        } else {
            return $form->getName();
        }
    }

    /**
     * @param Form $form
     *
     * @return array
     */
    protected function getValidationData(Form $form)
    {
        $result     = array();
        $parentData = array();
        $ownData    = array();
        $groups     = $this->getValidationGroups($form);

        // If parent has metadata
        $parent = $form->getParent();
        if ($parent && null !== $parent->getConfig()->getDataClass()) {
            $metadata = $this
                ->getMetadataFor($parent->getConfig()->getDataClass())
                ->getMemberMetadatas($form->getName());

            /** @var PropertyMetadata $item */
            foreach ($metadata as $item) {
                $this->composeValidationData(
                    $parentData,
                    $item->getConstraints(),
                    $getters = !empty($item->getters) ? (array)$item->getters : array()
                );
            }
        }
        // If has own metadata
        if (null !== $form->getConfig()->getDataClass()) {
            $metadata = $this->getMetadataFor($form->getConfig()->getDataClass());
            $this->composeValidationData(
                $ownData,
                $metadata->getConstraints(),
                $getters = !empty($metadata->getters) ? (array)$metadata->getters : array()
            );
        }
        // If has constraints in a form element
        $this->composeValidationData(
            $ownData,
            (array) $form->getConfig()->getOption('constraints'),
            array()
        );

        if ($parentData) {
            $result[$this->getValidationGroups($parent)] = $parentData;
        }
        if ($ownData) {
            $result[$groups] = $ownData;
        }

        return $result;
    }

    /**
     * @param array            $container
     * @param Constraint[]     $constraints
     * @param GetterMetadata[] $getters
     *
     * @return void
     */
    public function composeValidationData(&$container, $constraints, $getters)
    {
        if ($getters) {
            if (!isset($container['getters'])) {
                $container['getters'] = array();
            }
            $container['getters'] = array_merge($container['getters'], $this->parseGetters($getters));
        }
        if ($constraints) {
            if (!isset($container['constraints'])) {
                $container['constraints'] = array();
            }
            $container['constraints'] = array_merge($container['constraints'], $this->parseConstraints($constraints));
        }
    }

    /**
     * Get validation groups for the specified form
     *
     * @param Form|FormInterface $form
     *
     * @return array|string
     */
    protected function getValidationGroups(Form $form)
    {
        $result = JsModelAbstract::phpValueToJs(array('default'));
        $groups = $form->getConfig()->getOption('validation_groups');

        if (empty($groups)) {
            // Try to get groups from a parent
            if ($form->getParent()) {
                $result = $this->getValidationGroups($form->getParent());
            }
        } elseif (is_array($groups)) {
            // If groups is an array - return groups as is
            $result = JsModelAbstract::phpValueToJs($groups);
        } elseif ($groups instanceof \Closure) {
            // If groups is a Closure - return the form class name to look for javascript
            $result = get_class($form->getConfig()->getType()->getInnerType());
        }

        return $result;
    }

    /**
     * Not all elements should be processed by thy factory (e.g. buttons, hidden inputs etc)
     *
     * @param mixed $element
     *
     * @return bool
     */
    protected function isProcessableElement($element)
    {
        return ($element instanceof Form)
        && ('hidden' !== $element->getConfig()->getType()->getName());
    }

    /**
     * Convert transformers objects to data arrays
     *
     * @param array $transformers
     *
     * @return array
     */
    protected function parseTransformers(array $transformers)
    {
        $result = array();
        foreach ($transformers as $trans) {
            $item = array();

            $reflect    = new \ReflectionClass($trans);
            $properties = $reflect->getProperties();
            foreach ($properties as $prop) {
                $item[$prop->getName()] = $this->getTransformerParam($trans, $prop->getName());
            }

            $item['name'] = get_class($trans);

            $result[] = $item;
        }

        return $result;
    }

    /**
     * Get the specified non-public transformer property
     *
     * @param DataTransformerInterface $transformer
     * @param string                   $paramName
     *
     * @return mixed
     */
    protected function getTransformerParam(DataTransformerInterface $transformer, $paramName)
    {
        $reflection = new \ReflectionProperty($transformer, $paramName);
        $reflection->setAccessible(true);
        $value  = $reflection->getValue($transformer);
        $result = null;

        if ('transformers' === $paramName && is_array($value)) {
            $result = $this->parseTransformers($value);
        } elseif (is_scalar($value) || is_array($value)) {
            $result = $value;
        } elseif ($value instanceof ChoiceListInterface) {
            $result = $value->getChoices();
        }

        return $result;
    }

    /**
     * Converts list of the GetterMetadata objects to a data array
     *
     * @param GetterMetadata[] $getters
     *
     * @return array
     */
    protected function parseGetters(array $getters)
    {
        $result = array();
        foreach ($getters as $name => $getter) {
            $result[$name] = array(
                'class'       => $getter->getClassName(),
                'name'        => $getter->getName(),
                'constraints' => $this->parseConstraints((array)$getter->getConstraints()),
            );
        }

        return $result;
    }

    /**
     * Converts list of constraints objects to a data array
     *
     * @param array $constraints
     *
     * @return array
     */
    protected function parseConstraints(array $constraints)
    {
        $result = array();
        foreach ($constraints as $item) {
            // Translate messages if need and add to result
            foreach ($item as $propName => $propValue) {
                if (false !== strpos(strtolower($propName), 'message')) {
                    $item->{$propName} = $this->translateMessage($propValue);
                }
            }
            $result[get_class($item)][] = $item;
        }

        return $result;
    }
}