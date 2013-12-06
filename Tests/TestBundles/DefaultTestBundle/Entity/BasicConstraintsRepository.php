<?php

namespace Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Entity;

/**
 * Class BasicConstraintsRepository
 *
 * @package Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Entity
 */
class BasicConstraintsRepository
{
    /**
     * @var array
     */
    protected $data = array(
        array(
            'email' => 'wrong_email',
            'url'   => null,
            'ip'    => null
        ),
        array(
            'email' => 'existing_email',
            'url'   => 'existing_url',
            'ip'    => 'existing_ip'
        )
    );

    /**
     * This method just should renurn some not empty data
     *
     * @param string $id
     *
     * @return array
     */
    public function find($id)
    {
        return array('a' => $id);
    }

    /**
     * This method searches the data in the artificial storage of this repository
     *
     * @param array $criteria
     *
     * @return array
     */
    public function findBy(array $criteria)
    {
        $entities = array();
        foreach ($this->data as $entity) {
            $result = array();
            foreach ($criteria as $field => $value) {
                if ((isset($entity[$field]) || null == $entity[$field]) && $value == $entity[$field]) {
                    $result[$field] = $value;
                }
            }
            if ($result === $criteria) {
                $entities[] = $entity;
            }
        }

        return $entities;
    }
} 