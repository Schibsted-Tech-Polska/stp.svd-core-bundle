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
                ->arrayNode('error_pages')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('bundle')
                                ->isRequired()
                                ->example('SvdCoreBundle')
                            ->end()
                            ->scalarNode('controller')
                                ->defaultNull()
                                ->example('Error')
                            ->end()
                            ->arrayNode('formats')
                                ->defaultValue(array('html'))
                                ->beforeNormalization()->ifString()->then(function ($v) { return array($v); })->end()
                                ->prototype('scalar')->end()
                            ->end()
                            ->scalarNode('name')
                                ->defaultValue('%%code%%')
                                ->example('error_%%code%%')
                            ->end()
                            ->scalarNode('path')
                                ->isRequired()
                                ->example('^/path to resource/')
                            ->end()
                            ->arrayNode('view_vars')
                                ->defaultValue(array())
                                ->example('key: value')
                                ->prototype('scalar')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('locales')
                    ->addDefaultChildrenIfNoneSet()
                    ->prototype('scalar')
                        ->defaultValue('en')
                    ->end()
                ->end()
                ->arrayNode('manager')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('content_service')
                            ->defaultValue('svd_core.manager.content_real')
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('rediscloud')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('endpoint')
                            ->defaultNull()
                        ->end()
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
                ->arrayNode('session')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('expire')
                            ->defaultValue(3600)
                            ->example('time in seconds')
                            ->isRequired()
                        ->end()
                        ->scalarNode('prefix')
                            ->defaultValue('sess:')
                            ->isRequired()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
