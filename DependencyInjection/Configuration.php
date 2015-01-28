<?php

namespace Svd\CoreBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Dependency injection
 */
class Configuration implements ConfigurationInterface
{
    /**
     * Get config tree builder
     *
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('svd_core');

        $rootNode
            ->children()
                ->arrayNode('locales')
                    ->addDefaultChildrenIfNoneSet()
                    ->prototype('scalar')
                        ->defaultValue('en')
                    ->end()
                ->end()
                ->arrayNode('urls')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('admin_index')
                            ->defaultValue('admin_index')
                            ->isRequired()
                        ->end()
                        ->scalarNode('homepage')
                            ->defaultValue('homepage')
                            ->isRequired()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
