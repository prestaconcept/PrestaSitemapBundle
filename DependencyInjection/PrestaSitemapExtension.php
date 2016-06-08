<?php

/*
 * This file is part of the prestaSitemapPlugin package.
 * (c) David Epely <depely@prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 */
class PrestaSitemapExtension extends Extension
{
    /**
     * @inheritDoc
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');

        $container->setParameter($this->getAlias() . '.timetolive', $config['timetolive']);
        $container->setParameter($this->getAlias() . '.sitemap_file_prefix', $config['sitemap_file_prefix']);
        $container->setParameter($this->getAlias() . '.items_by_set', $config['items_by_set']);
        $container->setParameter($this->getAlias() . '.defaults', $config['defaults']);

        if (true === $config['route_annotation_listener']) {
            $loader->load('route_annotation_listener.xml');
        }

        $container->setAlias('presta_sitemap.generator', $config['generator']);
        $container->setAlias('presta_sitemap.dumper', $config['dumper']);
    }
}
