<?php

use Symfony\Component\Routing\RouteCollection;

$collection = new RouteCollection();
$collection->addCollection($loader->import("@DefaultTestBundle/Resources/config/routing.php"));
$collection->addCollection($loader->import("@FpJsFormValidatorBundle/Resources/config/routing.xml"));

return $collection;