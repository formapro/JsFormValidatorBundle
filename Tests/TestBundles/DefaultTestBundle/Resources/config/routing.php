<?php

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

$collection = new RouteCollection();

$controllerClass = 'Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Controller\FunctionalTestsController';
$uniqueEntityControllerClass = 'Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Controller\UniqueEntityController';

$collection->add(
    'fp_js_form_validator_phpinfo',
    new Route(
        '/fp_js_form_validator/test/phpinfo',
        array(
            '_controller' => $controllerClass . '::phpinfoAction',
        )
    )
);

$collection->add(
    'fp_js_form_validator_test_base',
    new Route(
        '/fp_js_form_validator/test/{controller}/{type}/{js}',
        array(
            '_controller' => $controllerClass . '::baseAction',
        )
    )
);

$collection->add(
    'fp_js_form_validator_unique_entity_controller',
    new Route(
        '/fp_js_form_validator/unique_entity_controller',
        array(
            '_controller' => $uniqueEntityControllerClass . '::indexAction',
        )
    )
);

return $collection;