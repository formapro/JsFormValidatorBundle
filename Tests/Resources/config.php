<?php
$container->loadFromExtension(
    'framework',
    array(
        'router' => array('resource' => 'routing.php'),
        'templating' => array('engines' => array('php')),
        'secret' => 'some value',
        'test' => true,
    )
);
$container->loadFromExtension(
    'mink',
    array(
        'base_url' => 'http://localhost',
        'selenium2' => array(),
    )
);