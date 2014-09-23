<?php

namespace Svd\CoreBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Dependency injection
 */
class SvdCoreExtension extends Extension
{
    /**
     * Load config and services
     *
     * @param array            $configs   configs
     * @param ContainerBuilder $container container builder
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        $container->setParameter('svd_core.locales', $config['locales']);
        $container->setParameter('svd_core.urls.admin_index', $config['urls']['admin_index']);
        $container->setParameter('svd_core.urls.homepage', $config['urls']['homepage']);
        $container->setParameter('svd_core.parameters.creation_year', $config['parameters']['creation_year']);
        $container->setParameter('svd_core.parameters.company_name', $config['parameters']['company_name']);
    }
}
