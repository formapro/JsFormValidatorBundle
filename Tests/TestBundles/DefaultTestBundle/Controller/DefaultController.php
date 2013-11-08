<?php

namespace Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Controller;

use Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Entity\Address;
use Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Entity\Book;
use Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Entity\User;
use Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/fp_js_form_validator/main_test")
     * @Template("DefaultTestBundle:Default:index.html.twig")
     */
    public function indexAction()
    {
        $namespase = 'Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Entity';
        $fixtures = array(
            'Role' => array(
                array(
                    'name' => 'user',
                ),
                array(
                    'name' => 'admin',
                ),
            )
        );

        $em = $this->getDoctrine()->getManager();
        foreach ($fixtures as $entityName => $entity) {
            $entityName = $namespase . '\\' . $entityName;
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

            foreach ($entity as $mapping) {
                if (!class_exists($entityName)) continue;
                $node = new $entityName();
                foreach ($mapping as $property => $value) {
                    if (!property_exists($node, $property)) continue;
                    $method = 'set' . ucfirst($property);
                    $node->{$method}($value);
                }
                $em->persist($node);
            }
        }
        $em->flush();


        $request = $this->getRequest();
        $user = new User();
        $addr1 = new Address();
//        $book1 = new Book();
//        $user->addAddress($addr1);
//        $user->addBook($book1);
        $form = $this->createForm(new UserType(), $user);

        if ($request->getMethod() == 'POST') {
            $form->submit($request);

            if ($form->isValid()) {
                var_dump('sucess');
            }
        }

//        $view = $form->createView();
//        $a = 1;

        return array(
            'form' => $form
        );
    }
}
