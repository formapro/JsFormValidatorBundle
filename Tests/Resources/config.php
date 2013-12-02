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
        'goutte' => array(),
        'base_url' => 'http://localhost',
        'sahi' => array(),
        'zombie' => array(),
        'selenium' => array(),
        'selenium2' => array(),
    )
);