<?php
/**
 * Created by PhpStorm.
 * User: Yury Maltsev
 * Email: dev.ymalcev@gmail.com
 * Date: 11/28/13
 * Time: 11:57 AM
 */

namespace Fp\JsFormValidatorBundle\Controller;


use Fp\JsFormValidatorBundle\Tests\BaseTestCase;
use Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Entity\BasicConstraintsEntity;
use Symfony\Component\HttpFoundation\JsonResponse;

class AjaxControllerTest extends BaseTestCase {
    public function testCheckUniqueEntityAction()
    {
        $data = array(
            'entity'           => 'Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Entity\BasicConstraintsEntity',
            'data'             => array(),
            'ignoreNull'       => '1',
            'repositoryMethod' => 'findBy'
        );
        $client = static::createClient();

        // Check a nonexistent email
        $data['data']['email'] = 'test_email';
        $client->request('POST', '/fp_js_form_validator/check_unique_entity', $data);
        $this->assertTrue(json_decode($client->getResponse()->getContent()));

        // Check an empty email
        $data['data']['email'] = null;
        $client->request('POST', '/fp_js_form_validator/check_unique_entity', $data);
        $this->assertTrue(json_decode($client->getResponse()->getContent()));

        // Check an existing email
        $data['data']['email'] = 'existing_email';
        $client->request('POST', '/fp_js_form_validator/check_unique_entity', $data);
        $this->assertFalse(json_decode($client->getResponse()->getContent()));

        // Check the identical pair
        $data['data']['email'] = 'existing_email';
        $data['data']['url'] = 'existing_url';
        $client->request('POST', '/fp_js_form_validator/check_unique_entity', $data);
        $this->assertFalse(json_decode($client->getResponse()->getContent()));

        // Check the pair with ignore null
        $data['data']['email'] = 'wrong_email';
        $data['data']['url'] = null;
        $client->request('POST', '/fp_js_form_validator/check_unique_entity', $data);
        $this->assertTrue(json_decode($client->getResponse()->getContent()));

        // Check the pair without ignore null
        $data['ignoreNull'] = '0';
        $client->request('POST', '/fp_js_form_validator/check_unique_entity', $data);
        $this->assertFalse(json_decode($client->getResponse()->getContent()));

        // Check the another repository method
        $data['repositoryMethod'] = 'find';
        $client->request('POST', '/fp_js_form_validator/check_unique_entity', $data);
        $this->assertFalse(json_decode($client->getResponse()->getContent()));
    }
} 