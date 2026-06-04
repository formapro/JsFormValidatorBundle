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
     * @var int|string|null
     */
    public $entityId = null;

    /**
     * @var mixed
     */
    protected $entity = null;

    /**
     * @param BaseUniqueEntity $base
     * @param string           $entityName
     * @param mixed            $entity
     */
    public function __construct(BaseUniqueEntity $base, $entityName, $entity = null)
    {
        foreach (get_object_vars($base) as $prop => $value) {
            $this->{$prop} = $value;
        }

        $this->entityName = $entityName;
        if (is_object($entity)) {
            $this->entity = $entity;
            if (method_exists($entity, 'getId')) {
                $this->entityId = $entity->getId();
            }
        }
    }
}
