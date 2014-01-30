<?php

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

$collection = new RouteCollection();

$controllerClass = 'Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Controller\FunctionalTestsController';

$collection->add(
    'fp_js_form_validator_test_translations',
    new Route(
        '/fp_js_form_validator/javascript_unit_test/translations/{domain}/{js}',
        array(
            '_controller' => $controllerClass . '::translationAction',
        )
    )
);

$collection->add(
    'fp_js_form_validator_test_nesting',
    new Route(
        '/fp_js_form_validator/javascript_unit_test/nesting/{type}/{js}',
        array(
            '_controller' => $controllerClass . '::nestingAction',
        )
    )
);

$collection->add(
    'fp_js_form_validator_test_unique_entity',
    new Route(
        '/fp_js_form_validator/javascript_unit_test/unique_entity/{isValid}/{js}',
        array(
            '_controller' => $controllerClass . '::uniqueEntityAction',
        )
    )
);

$collection->add(
    'fp_js_form_validator_test_basic_constraints',
    new Route(
        '/fp_js_form_validator/javascript_unit_test/basic_constraints/{isValid}/{js}',
        array(
            '_controller' => $controllerClass . '::basicConstraintsAction',
        )
    )
);

$collection->add(
    'fp_js_form_validator_test_transformers',
    new Route(
        '/fp_js_form_validator/javascript_unit_test/transformers/{isValid}/{js}',
        array(
            '_controller' => $controllerClass . '::transformersAction',
        )
    )
);

$collection->add(
    'fp_js_form_validator_test_part',
    new Route(
        '/fp_js_form_validator/javascript_unit_test/part/{js}',
        array(
            '_controller' => $controllerClass . '::partOfFormAction',
        )
    )
);

$collection->add(
    'fp_js_form_validator_test_empty',
    new Route(
        '/fp_js_form_validator/javascript_unit_test/empty/{js}',
        array(
            '_controller' => $controllerClass . '::emptyElementsAction',
        )
    )
);

return $collection;