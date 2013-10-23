<?php

namespace Fp\JsFormValidatorBundle\Tests\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Entity\TestEntity;
use Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Form\TestEntityType;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Validator\Constraints\GreaterThan;

class JsFormValidatorTest extends WebTestCase
{
    /**
     * @test
     */
    public function testConstraints()
    {
        $client = static::createClient();
        $this->assertNotNull($fm = $client->getContainer()->get('fp_js_form_validator.form_manager'));
        $entity = new TestEntity();
        $factory = $client->getContainer()->get('form.factory');

        // --------------------------------------------------------//
        // Get form without group
        // Expect to receive:
        // - default constraints from entity
        // - all constraints from form class
        // --------------------------------------------------------//
        $form = $factory->create(new TestEntityType(), $entity);
        $this->checkAssertCollection($fm->getAllConstraints($form), [
            'email'    => 1,
            'password' => 1,
            'created'  => 1
        ]);

        // --------------------------------------------------------//
        // Get form for group_1 & group_2
        // Expect to receive:
        // - the "default", "group_1" and "group_2" constraints from entity
        // - all constraints from form class
        // - one more added constraint below
        // --------------------------------------------------------//
        $form = $factory->create(new TestEntityType(), $entity, [
            'validation_groups' => ['group_1', 'group_2']
        ]);
        $form->add('age', 'text', [
            'constraints' => [
                new GreaterThan(['value' => 18])
            ]
        ]);
        $this->checkAssertCollection($fm->getAllConstraints($form), [
            'name'     => 2,
            'email'    => 1,
            'password' => 1,
            'created'  => 1,
            'age'      => 1
        ]);

        // --------------------------------------------------------//
        // Get form for group_6 and try to remove some elements
        // Expect to receive:
        // - constraints for dynamically added "rate" field
        // - all constraints from form class
        // --------------------------------------------------------//
        $form = $factory->create(new TestEntityType(), $entity, [
            'validation_groups' => ['group_6']
        ]);
        $form->add('rate');
        $form->remove('email');
        $form->remove('password');
        $this->checkAssertCollection($fm->getAllConstraints($form), [
            'created'  => 1,
            'rate'     => 1
        ]);
    }

    /**
     * @param ArrayCollection $collection
     * @param array $expects
     */
    private function checkAssertCollection(ArrayCollection $collection, $expects)
    {
        $this->assertCount(count($expects), $collection->toArray(), 'Wrong number of received fields');
        foreach ($expects as $field => $cntConstr) {
            $this->assertNotNull($constrs = $collection->get($field), 'Can not find the expected field "'.$field.'"');
            $this->assertCount($cntConstr, $constrs, 'Wrong number of constraints for the "'.$field.'" field');
        }
    }
}
