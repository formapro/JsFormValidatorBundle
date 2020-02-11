<?php

namespace Fp\JsFormValidatorBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\HttpKernel\Kernel;

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
        // A tree builder without a root node is deprecated since Symfony 4.2 and will not be supported anymore in 5.0.
        if (Kernel::VERSION_ID < 40212) {
            $treeBuilder = new TreeBuilder();
            $rootNode = $treeBuilder->root('fp_js_form_validator');
        } else {
            $treeBuilder = new TreeBuilder('fp_js_form_validator');
            $rootNode = $treeBuilder->getRootNode();
        }

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
