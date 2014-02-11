<?php
/**
 * Created by PhpStorm.
 * User: Yury Maltsev
 * Email: dev.ymalcev@gmail.com
 * Date: 2/11/14
 * Time: 2:35 PM
 */

namespace Fp\JsFormValidatorBundle\Tests\Unit;


use Fp\JsFormValidatorBundle\Model\JsConfig;
use Fp\JsFormValidatorBundle\Tests\BaseTestCase;
use Fp\JsFormValidatorBundle\Tests\Fixtures\Entity;

class JsModleAbstractUnitTest extends BaseTestCase
{
    public function testToString()
    {
        $entity = new Entity();
        $entity->setName('Test');

        $model = new JsConfig();
        $model->routing = array(
            'string' => 'Test',
            'entity' => $entity
        );

        $result = '' . $model;

        $this->assertEquals("{'routing':{'string':'Test','entity':'Test'}}", $result, 'ToString object convers properly');
    }
} 