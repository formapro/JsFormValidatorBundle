<?php
/**
 * Created by PhpStorm.
 * User: Yury Maltsev
 * Email: dev.ymalcev@gmail.com
 * Date: 11/14/13
 * Time: 10:17 AM
 */

namespace Fp\JsFormValidatorBundle\Tests;


use Behat\MinkBundle\Test\MinkTestCase;
use Doctrine\ORM\EntityManager;

class BaseTestCase extends  MinkTestCase {
    /**
     * Open no public methods
     *
     * @param string $obj
     * @param string $methodName
     * @param array $args
     *
     * @return mixed
     */
    protected function callNoPublicMethod($obj, $methodName, array $args = array())
    {
        $class  = new \ReflectionClass($obj);
        $method = $class->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($obj, $args);
    }

    protected function clearDbTables($tables)
    {
        $client = static::createClient();
        $tables = is_string($tables) ? array($tables) : $tables;
        $namespase = 'Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Entity';
        /** @var EntityManager $em */
        $em = $client->getContainer()->get('doctrine')->getManager();
        foreach ($tables as $name) {
            $entityName = $namespase . '\\' . $name;
            $cmd = $em->getClassMetadata($entityName);
            $connection = $em->getConnection();
            $dbPlatform = $connection->getDatabasePlatform();
            $connection->beginTransaction();
            try {
                $connection->query('SET FOREIGN_KEY_CHECKS=0');
                $q = $dbPlatform->getTruncateTableSql($cmd->getTableName());
                $connection->executeUpdate($q);
                $connection->query('SET FOREIGN_KEY_CHECKS=1');
                $connection->commit();
            }
            catch (\Exception $e) {
                $connection->rollback();
            }
        }
    }

    protected function writeEntityToDb($name, $mapping)
    {
        $client = static::createClient();
        /** @var EntityManager $em */
        $em = $client->getContainer()->get('doctrine')->getManager();

        $namespase = 'Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Entity';
        $entityName = $namespase . '\\' . $name;

        if (!class_exists($entityName)) return null;

        $node = new $entityName();
        foreach ($mapping as $property => $value) {
            if (!property_exists($node, $property)) continue;
            $methodAdd = 'add' . ucfirst($property);
            $methodSet = 'set' . ucfirst($property);
            if (method_exists($node, $methodAdd)) {
                $node->{$methodAdd}($value);
            } elseif (method_exists($node, $methodSet)) {
                $node->{$methodSet}($value);
            }
        }

        $em->persist($node);
        $em->flush();
    }
} 