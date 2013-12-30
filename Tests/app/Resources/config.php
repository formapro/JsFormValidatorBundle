<?php
$container->loadFromExtension('framework', array(
    'translator' => array('fallback' => 'en'),
    'secret' => 'some value',
    'router' => array('resource' => 'routing.php'),
    'form' => array(),
    'csrf_protection' => array(),
    'validation' => array('enable_annotations' => true),
    'templating' => array('engines' => array('twig', 'php')),
    'default_locale' => 'en',
    'trusted_proxies' => array(),
    'session' => array(),
    'fragments' => array(),
    'http_method_override' => true,
    'test' => true,
));
$container->loadFromExtension('twig', array(
    'debug' => true,
    'strict_variables' => true,
));
$container->loadFromExtension('doctrine', array(
    'orm' => array(
        'auto_mapping' => true
    ),
    'dbal' => array()
));
$container->loadFromExtension(
    'mink',
    array(
        'browser_name' => 'chrome',
        'base_url' => 'http://jsfv.int/index.php',
        'selenium2' => array(),
    )
);
