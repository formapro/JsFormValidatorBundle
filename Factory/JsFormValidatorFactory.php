<?php
namespace Fp\JsFormValidatorBundle\Factory;

use Fp\JsFormValidatorBundle\Form\Constraint\UniqueEntity;
use Fp\JsFormValidatorBundle\Model\JsConfig;
use Fp\JsFormValidatorBundle\Model\JsFormElement;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity as BaseUniqueEntity;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceListInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Mapping\GetterMetadata;
use Symfony\Component\Validator\Mapping\PropertyMetadata;
use Symfony\Component\Validator\ValidatorInterface;

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
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var UrlGeneratorInterface
     */
    protected $router;

    /**
     * @var array
     */
    protected $config = array();

    /**
     * @var Form[]
     */
    protected $queue = array();

    /**
     * @var Form
     */
    protected $currentElement = null;

    /**
     * @var string
     */
    protected $transDomain;

    /**
     * @param ValidatorInterface    $validator
     * @param TranslatorInterface   $translator
     * @param UrlGeneratorInterface $router
     * @param array                 $config
     * @param string                $domain
     */
    public function __construct(
        ValidatorInterface $validator,
        TranslatorInterface $translator,
        UrlGeneratorInterface $router,
        $config,
        $domain
    ) {
        $this->validator   = $validator;
        $this->translator  = $translator;
        $this->router      = $router;
        $this->config      = $config;
        $this->transDomain = $domain;
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
        return $this->translator->trans($message, array(), $this->transDomain);
    }

    /**
     * Generate an URL from the route
     *
     * @param string $route
     *
     * @return string
     * @codeCoverageIgnore
     */
    protected function generateUrl($route)
    {
        return $this->router->generate($route);
    }

    /**
     * Get Config
     *
     * @param null|string $name
     *
     * @return mixed
     */
    public function getConfig($name = null)
    {
        if ($name) {
            return isset($this->config[$name]) ? $this->config[$name] : null;
        } else {
            return $this->config;
        }
    }

    public function createJsConfigModel()
    {
        $result = array();
        if (!empty($this->config['routing'])) {
            foreach ($this->config['routing'] as $param => $value) {
                try {
                    $result['routing'][$param] = $this->generateUrl($value);
                } catch (\Exception $e) {
                    $result['routing'][$param] = null;
                }
            }
        }
        $model          = new JsConfig;
        $model->routing = $result['routing'];

        return $model;
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
     * Check if form is already in queue
     *
     * @param Form $form
     *
     * @return bool
     */
    public function inQueue(Form $form)
    {
        return isset($this->queue[$form->getName()]);
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
        $this->currentElement = $form;

        $conf = $form->getConfig();
        // If field is disabled or has no any validations
        if (false === $conf->getOption('js_validation')) {
            return null;
        }

        $model                 = new JsFormElement;
        $model->id             = $this->getElementId($form);
        $model->name           = $form->getName();
        $model->type           = $conf->getType()->getInnerType()->getName();
        $model->invalidMessage = $conf->getOption('invalid_message');
        $model->transformers   = $this->parseTransformers($form->getConfig()->getViewTransformers());
        $model->cascade        = $conf->getOption('cascade_validation');
        $model->bubbling       = $conf->getOption('error_bubbling');
        $model->data           = $this->getValidationData($form);
        $model->children       = $this->processChildren($form);

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
        // If parent has metadata
        $parent = $form->getParent();
        if ($parent && null !== $parent->getConfig()->getDataClass()) {
            $classMetadata = $metadata = $this->getMetadataFor($parent->getConfig()->getDataClass());
            if ($classMetadata->hasMemberMetadatas($form->getName())) {
                $metadata = $classMetadata->getMemberMetadatas($form->getName());
                /** @var PropertyMetadata $item */
                foreach ($metadata as $item) {
                    $this->composeValidationData(
                        $parentData,
                        $item->getConstraints(),
                        $getters = !empty($item->getters) ? (array)$item->getters : array()
                    );
                }
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
            $formData,
            (array)$form->getConfig()->getOption('constraints'),
            array()
        );

        $result = array();
        $groups = $this->getValidationGroups($form);

        if (!empty($parentData)) {
            $parentData['groups'] = $this->getValidationGroups($parent);
            $result['parent']     = $parentData;
        }
        if (!empty($ownData)) {
            $ownData['groups'] = $groups;
            $result['entity']  = $ownData;
        }
        if (!empty($formData)) {
            $formData['groups'] = $groups;
            $result['form']     = $formData;
        }

        return $result;
    }

    protected function mergeDataRecursive(array $array1, array $array2)
    {
        foreach ($array2 as $key => $value) {
            if (empty($array1[$key])) {
                $array1[$key] = $value;
            } elseif (is_array($value)) {
                if ((array_keys($value) !== range(0, count($value) - 1))) {
                    $array1[$key] = $this->mergeDataRecursive($array1[$key], $value);
                } else {
                    $array1[$key] = array_merge($array1[$key], $value);
                }
            } else {
                $array1[$key] = $value;
            }
        }

        return $array1;
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
        if (null == $container) {
            $container = array();
        }
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
        $result = array('Default');
        $groups = $form->getConfig()->getOption('validation_groups');

        if (empty($groups)) {
            // Try to get groups from a parent
            if ($form->getParent()) {
                $result = $this->getValidationGroups($form->getParent());
            }
        } elseif (is_array($groups)) {
            // If groups is an array - return groups as is
            $result = $groups;
        } elseif ($groups instanceof \Closure) {
            // If groups is a Closure - return the form class name to look for javascript
            $result = $this->getElementId($form);
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
        foreach ($getters as $getter) {
            $result[$getter->getName()] = $this->parseConstraints((array)$getter->getConstraints());
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

            if ($item instanceof BaseUniqueEntity) {
                $item = new UniqueEntity($item, $this->currentElement->getConfig()->getDataClass());
            }

            $result[get_class($item)][] = $item;
        }

        return $result;
    }
}