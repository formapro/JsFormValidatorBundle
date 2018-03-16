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
     * @var int
     */
    public $entityId = null;

    /**
     * @param BaseUniqueEntity $base
     * @param string           $entityName
     * @param mixed            $data
     */
    public function __construct(BaseUniqueEntity $base, $entityName, $data)
    {
        $this->entityName = $entityName;

        if (is_object($data) && method_exists($data, 'getId')) {
            $this->entityId = $data->getId();
        }

        foreach ($base as $prop => $value) {
            $this->{$prop} = $value;
        }
    }
} 