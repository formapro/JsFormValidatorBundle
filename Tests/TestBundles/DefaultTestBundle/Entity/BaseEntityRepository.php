<?php
/**
 * Created by PhpStorm.
 * User: Yury Maltsev
 * Email: dev.ymalcev@gmail.com
 * Date: 1/29/14
 * Time: 2:57 PM
 */

namespace Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Entity;


abstract class BaseEntityRepository
{
    abstract public function getData();

    /**
     * This method just should return some not empty data
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
        foreach ($this->getData() as $entity) {
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