<?php

use Doctrine\Common\Annotations\AnnotationRegistry;

/** @noinspection PhpIncludeInspection */
$loader = require __DIR__.'/../../vendor/autoload.php';

AnnotationRegistry::registerLoader(array($loader, 'loadClass'));

return $loader;