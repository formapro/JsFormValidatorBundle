<?php

use Symfony\Component\Routing\RouteCollection;

$collection = new RouteCollection();
$collection->addCollection($loader->import("@DefaultTestBundle/Resources/config/routing.php"));

/** @var Symfony\Component\Routing\RouteCollection $nativeRoutes */
$nativeRoutes = $loader->import(
    "@FpJsFormValidatorBundle/Resources/config/routing.xml"
);
$nativeRoutes->addPrefix('/fp_js_form_validator');
$collection->addCollection($nativeRoutes);

return $collection;