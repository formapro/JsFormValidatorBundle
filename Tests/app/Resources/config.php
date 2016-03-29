<?php
$defaults = array(
    'mink' => array(
        'browser_name' => 'firefox',
        'base_url' => 'http://localhost/index.php',
    ),
);

if (file_exists( __DIR__ . '/local_config.php')) {
    $localConfig = include_once __DIR__ . '/local_config.php';
    foreach ($localConfig as $name => $opts) {
        $defaults[$name] = array_merge($defaults[$name], $localConfig[$name]);
    }
}

$bundleConfig = array();

// This trick uses to test different translation domains
$env = $container->getParameter('kernel.environment');
switch ($env) {
    case 'trans':
        /** @var string $transDomain */
        $container->setParameter('validator.translation_domain', 'test');
        break;
    case 'disable':
        $bundleConfig['js_validation'] = false;
        break;
    case 'unique':
        $bundleConfig['routing'] = array(
            'check_unique_entity' => 'fp_js_form_validator_unique_entity_controller'
        );
        break;
    default:
        break;
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
    'assets' => array(),
));
$container->loadFromExtension('twig', array(
    'debug' => true,
    'strict_variables' => true,
    'form_themes' => array('DefaultTestBundle::form_theme.html.twig')
));
$container->loadFromExtension('doctrine', array(
    'orm' => array(
        'auto_mapping' => true
    ),
    'dbal' => array(
        'connections' => array(
            'default' => array(
                'driver'   => 'pdo_sqlite',
            )
        )
    )
));
$container->loadFromExtension(
    'mink',
    array(
        'browser_name' => $defaults['mink']['browser_name'],
        'base_url' => $defaults['mink']['base_url'],
        'selenium2' => array(),
    )
);

if (!empty($bundleConfig)) {
    $container->loadFromExtension('fp_js_form_validator', $bundleConfig);
}

$container
    ->register('fp_js_validator.test_extension', 'Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Twig\Extension\TestTwigExtension')
    ->addArgument(new \Symfony\Component\DependencyInjection\Reference('kernel'))
    ->addTag('twig.extension');
