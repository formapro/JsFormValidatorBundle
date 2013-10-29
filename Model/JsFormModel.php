<?php
/**
 * Created by PhpStorm.
 * User: Yury Maltsev
 * Email: dev.ymalcev@gmail.com
 * Date: 10/24/13
 * Time: 3:14 PM
 */

namespace Fp\JsFormValidatorBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Fp\JsFormValidatorBundle\Validator\Constraints\Repeated;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormView;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Mapping\GetterMetadata;
use Symfony\Component\Validator\Mapping\PropertyMetadata;

class JsFormModel {
    /**
     * @var string
     */
    public $id;

    /**
     * @var array
     */
    public $groups = ['Default'];

    /**
     * @var array
     */
    public $fields = [];

    /**
     * @var array
     */
    public $methods = [];

    /**
     * @param string $id
     * @param array $groups
     */
    public function __construct($id, array $groups)
    {
        $this->id   = $id;
        $this->groups = array_merge($groups, $this->groups);
    }

    /**
     * @param FormView|PropertyMetadata $field
     * @param null|Translator $translator
     *
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
     * @return $this
     */
    public function addField($field, $translator = null)
    {
        $type = null;

        if ($field instanceof PropertyMetadata) {
            $id = $this->id . '_' . $field->getName();
            $constraints = (array) $field->getConstraints();
        } elseif ($field instanceof FormView) {
            $opts = new ArrayCollection($field->vars);
            $id = $opts->get('id');
            $constraints = (array) $opts->get('constraints');
            $type = $opts->get('type');
        } else {
            throw new UnexpectedTypeException($field, 'Symfony\Component\Form\Form, Symfony\Component\Form\FormView, Symfony\Component\Validator\Mapping\PropertyMetadata');
        }

        // Add an extra validation element for the RepeatedType elements
        $repeatedType = new RepeatedType();
        if ($type == $repeatedType->getName()) {
            $constr = new Repeated();
            $constr->populate($field);
            $constraints[] = $constr;
            $id = $constr->getId();
        }

        /** @var $constr Constraint */
        foreach ($constraints as $constr) {
            $mutual = array_intersect($this->groups, $constr->groups);
            if (!empty($mutual)) {
                if (null !== $translator) {
                    foreach ($constr as $propName => $propValue) {
                        if (strpos(strtolower($propName), 'message') !== false) {
                            $constr->{$propName} = $translator->trans($propValue, [], 'validators');
                        }
                    }
                }
                $constrName = str_replace('\\', '', get_class($constr));
                $this->fields[$id][$constrName][] = $constr;
            }
        }

        return $this;
    }

    /**
     * @param GetterMetadata $getter
     *
     * @return $this
     */
    public function addMethod(GetterMetadata $getter)
    {
        $constraints = (array) $getter->getConstraints();
        /** @var $constr Constraint */
        foreach ($constraints as $constr) {
            $mutual = array_intersect($this->groups, $constr->groups);
            if (!empty($mutual)) {
                $entityName = str_replace('\\', '', $getter->getClassName());
                $constrName = str_replace('\\', '', get_class($constr));
                $this->methods[$entityName][$getter->getName()][$constrName][] = $constr;
            }
        }
    }

    /**
     * @param string $name
     * @param array|Constraint $constraints
     * @param string $list
     *
     * @return $this
     */
    public function addConstraintsToList($name, $constraints, $list = 'fields')
    {
        if ($constraints instanceof Constraint) {
            $constraints = [$constraints];
        } elseif (null === $constraints) {
            return $this;
        }

        /** @var $constr Constraint */
        foreach ($constraints as $constr) {
            $mutual = array_intersect($this->groups, $constr->groups);
            if (!empty($mutual)) {
                $this->{$list}[$name][get_class($constr)][] = $constr;
            }
        }

        return $this;
    }
} 