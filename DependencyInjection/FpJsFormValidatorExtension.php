<?php

namespace Fp\JsFormValidatorBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages this bundle configuration
 */
class FpJsFormValidatorExtension extends Extension
{
    /**
     * Load configuration
     * @param array            $configs
     * @param ContainerBuilder $container
     *
     * @codeCoverageIgnore
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter($this->getAlias() . '.config', $config);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');
    }

    /**
     * @codeCoverageIgnore
     * @return string
     */
    public function getAlias()
    {
        return 'fp_js_form_validator';
    }
}
