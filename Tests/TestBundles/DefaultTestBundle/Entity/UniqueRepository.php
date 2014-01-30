<?php

namespace Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Entity;

/**
 * Class BasicConstraintsRepository
 *
 * @package Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Entity
 */
class UniqueRepository extends BaseEntityRepository
{
    public function getData()
    {
        return array(
            array(
                'email' => 'existing_email',
                'name'  => 'existing_name',
            )
        );
    }
}