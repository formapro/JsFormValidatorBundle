<?php
namespace Fp\JsFormValidatorBundle\Form\Constraint;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity as BaseUniqueEntity;

/**
 * Class UniqueEntity
 * @package Fp\JsFormValidatorBundle\Form\Constraint
 */
class UniqueEntity extends BaseUniqueEntity
{
    /**
     * @var string
     */
    public $entityName = null;

    /**
     * @var mixed
     */
    protected $entity = null;

    /**
     * @var int
     */
    public $entityId = null;

    /**
     * @param BaseUniqueEntity $base
     * @param string           $entityName
     * @param mixed            $entity
     */
    public function __construct(BaseUniqueEntity $base, $entityName, $entity)
    {
        $this->entityName = $entityName;

        if (is_object($entity)) {
            $this->entity = $entity;

            if (method_exists($this->entity, 'getId')) {
                $this->entityId = $this->entity->getId();
            }
        }

        foreach ($base as $prop => $value) {
            $this->{$prop} = $value;
        }
    }
} 