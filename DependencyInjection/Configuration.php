<?php

namespace Fp\JsFormValidatorBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from the app/config files
 */
class Configuration implements ConfigurationInterface
{
    /**
     * @codeCoverageIgnore
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('fp_js_form_validator');

        /** @noinspection PhpUndefinedMethodInspection */
        $rootNode
            ->children()
                ->scalarNode('js_validation')
                    ->defaultValue(true)
                ->end()
                ->arrayNode('routing')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('check_unique_entity')
                            ->isRequired()
                            ->cannotBeEmpty()
                            ->defaultValue('fp_js_form_validator.check_unique_entity')
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
