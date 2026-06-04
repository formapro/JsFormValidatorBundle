<?php

namespace Fp\JsFormValidatorBundle\Tests\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectRepository;
use Fp\JsFormValidatorBundle\Controller\AjaxController;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Class AjaxControllerTest
 *
 * @package Fp\JsFormValidatorBundle\Controller
 */
class AjaxControllerTest extends TestCase
{
    /**
     * Test action to check UniqueEntity constraint
     */
    public function testCheckUniqueEntityAction()
    {
        $data   = array(
            'entityName'       => 'Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Entity\BasicConstraintsEntity',
            'data'             => array(),
            'ignoreNull'       => '1',
            'repositoryMethod' => 'findBy'
        );
        $repository = new InMemoryRepository();
        $controller = new AjaxController($this->createRegistry($repository));

        // Check a nonexistent email
        $data['data']['email'] = 'test_email';
        $response = $controller->checkUniqueEntityAction(new Request(array(), $data));
        $this->assertTrue(json_decode($response->getContent()), 'A nonexistent is unique');

        // Check an empty email
        $data['data']['email'] = null;
        $response = $controller->checkUniqueEntityAction(new Request(array(), $data));
        $this->assertTrue(json_decode($response->getContent()), 'An empty email is unique');

        // Check an existing email
        $data['data']['email'] = 'existing_email';
        $response = $controller->checkUniqueEntityAction(new Request(array(), $data));
        $this->assertFalse(json_decode($response->getContent()), 'An existing email is NOT unique');

        // Check the identical pair
        $data['data']['email'] = 'existing_email';
        $data['data']['url']   = 'existing_url';
        $response = $controller->checkUniqueEntityAction(new Request(array(), $data));
        $this->assertFalse(json_decode($response->getContent()), 'A pair of fields is NOT unique');

        // Check the pair with ignore null
        $data['data']['email'] = 'wrong_email';
        $data['data']['url']   = null;
        $response = $controller->checkUniqueEntityAction(new Request(array(), $data));
        $this->assertTrue(
            json_decode($response->getContent()),
            'A pair of fields is unique where one of them is empty and ignoreNull = true'
        );

        // Check the pair without ignore null
        $data['ignoreNull'] = '0';
        $response = $controller->checkUniqueEntityAction(new Request(array(), $data));
        $this->assertFalse(
            json_decode($response->getContent()),
            'A pair of fields is NOT unique where one of them is empty and ignoreNull = false'
        );

        // Check the another repository method
        $data['repositoryMethod'] = 'find';
        $response = $controller->checkUniqueEntityAction(new Request(array(), $data));
        $this->assertFalse(json_decode($response->getContent()), 'Another repository method works');
    }

    public function testDoctrineIsRequired()
    {
        $this->expectException(\LogicException::class);

        $controller = new AjaxController();
        $controller->checkUniqueEntityAction(new Request(array(), array()));
    }

    public function testUniqueEntityRequestDataIsRequired()
    {
        $this->expectException(BadRequestHttpException::class);

        $repository = new InMemoryRepository();
        $controller = new AjaxController($this->createRegistry($repository));

        $controller->checkUniqueEntityAction(new Request(array(), array()));
    }

    private function createRegistry(ObjectRepository $repository)
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $registry
            ->method('getRepository')
            ->willReturn($repository)
        ;

        return $registry;
    }
}

class InMemoryEntity
{
}

class InMemoryRepository implements ObjectRepository
{
    public function find(mixed $id): ?object
    {
        return (object) array('id' => $id);
    }

    public function findAll(): array
    {
        return array();
    }

    public function findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): array
    {
        if (isset($criteria['email']) && 'existing_email' === $criteria['email']) {
            return array((object) $criteria);
        }

        if (array_key_exists('url', $criteria) && null === $criteria['url']) {
            return array((object) $criteria);
        }

        return array();
    }

    public function findOneBy(array $criteria): ?object
    {
        return null;
    }

    /**
     * @return class-string<object>
     */
    public function getClassName(): string
    {
        return InMemoryEntity::class;
    }
}
