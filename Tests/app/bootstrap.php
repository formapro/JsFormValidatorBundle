<?php

use Doctrine\Common\Annotations\AnnotationRegistry;

$files = array(
    'local'  => __DIR__ . '/../../vendor/autoload.php',
    'global' => __DIR__ . '/../../../../../../autoload.php',
);
foreach ($files as $file) {
    if (file_exists($file)) {
        /** @noinspection PhpIncludeInspection */
        $loader = include $file;
        break;
    }
}

AnnotationRegistry::registerLoader(array($loader, 'loadClass'));

return $loader;