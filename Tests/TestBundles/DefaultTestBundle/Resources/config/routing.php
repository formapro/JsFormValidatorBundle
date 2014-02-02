<?php

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

$collection = new RouteCollection();

$controllerClass = 'Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Controller\FunctionalTestsController';

$collection->add(
    'fp_js_form_validator_test_base',
    new Route(
        '/fp_js_form_validator/test/{controller}/{type}/{js}',
        array(
            '_controller' => $controllerClass . '::baseAction',
        )
    )
);

return $collection;