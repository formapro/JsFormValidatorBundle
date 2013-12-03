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
    public function findBy($criteria) {
        if ($criteria['email'] == 'wrong_email') {
            return true;
        } else {
            return null;
        }
    }
} 