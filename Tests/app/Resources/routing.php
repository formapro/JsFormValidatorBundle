<?php

use Symfony\Component\Routing\RouteCollection;

$collection = new RouteCollection();
$collection->addCollection($loader->import("@DefaultTestBundle/Resources/config/routing.php"));

$nativeRoutes = $loader->import(
    "@FpJsFormValidatorBundle/Resources/config/routing.xml"
);
$nativeRoutes->setPrefix('/fp_js_form_validator');
$collection->addCollection($loader->import("@FpJsFormValidatorBundle/Resources/config/routing.xml"));

return $collection;