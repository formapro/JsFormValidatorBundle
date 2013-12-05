<?php
/**
 * Created by PhpStorm.
 * User: Yury Maltsev
 * Email: dev.ymalcev@gmail.com
 * Date: 12/3/13
 * Time: 12:00 PM
 */

namespace Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Entity;


class BasicConstraintsRepository {
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
     * @param $id
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
     * @param $criteria
     *
     * @return array
     */
    public function findBy($criteria) {
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