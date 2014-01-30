<?php
$defaults = array(
    'mink' => array(
        'browser_name' => 'firefox',
        'base_url' => 'http://localhost/index.php',
    ),
);

if (file_exists( __DIR__ . '/local_config.php')) {
    $localConfig = include_once __DIR__ . '/local_config.php';
    $defaults = array_merge($defaults, (array) $localConfig);
}

// This trick uses to test different translation domains
if (!empty($_SERVER['REQUEST_URI'])) {
    preg_match('/javascript_unit_test\/translations\/(\w+)\/\d/', $_SERVER['REQUEST_URI'], $testTranslationParameters);
    if ($testTranslationParameters && 'default' != $testTranslationParameters[1]) {
        $container->setParameter('validator.translation_domain', $testTranslationParameters[1]);
    }
}


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
        'browser_name' => $defaults['mink']['browser_name'],
        'base_url' => $defaults['mink']['base_url'],
        'selenium2' => array(),
    )
);
