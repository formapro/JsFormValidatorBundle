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
     * @param BaseUniqueEntity $base
     * @param string           $entityName
     */
    public function __construct(BaseUniqueEntity $base, $entityName)
    {
        $this->entityName = $entityName;

        foreach ($base as $prop => $value) {
            $this->{$prop} = $value;
        }
    }
} 