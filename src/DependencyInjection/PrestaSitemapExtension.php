<?php

/*
 * This file is part of the PrestaSitemapBundle package.
 *
 * (c) PrestaConcept <https://prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle\DependencyInjection;

use Presta\SitemapBundle\Event\SitemapAddUrlEvent;
use Presta\SitemapBundle\Event\SitemapPopulateEvent;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * Load Bundle configuration, configure container parameters & services.
 */
class PrestaSitemapExtension extends Extension
{
    /**
     * @inheritDoc
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__ . '/../../config'));
        $loader->load('services.xml');

        $container->setParameter($this->getAlias() . '.dump_directory', (string)$config['dump_directory']);
        $container->setParameter($this->getAlias() . '.timetolive', (int)$config['timetolive']);
        $container->setParameter($this->getAlias() . '.sitemap_file_prefix', (string)$config['sitemap_file_prefix']);
        $container->setParameter($this->getAlias() . '.items_by_set', (int)$config['items_by_set']);
        $container->setParameter($this->getAlias() . '.defaults', $config['defaults']);
        $container->setParameter($this->getAlias() . '.default_section', (string)$config['default_section']);

        if (true === $config['route_annotation_listener']) {
            $loader->load('route_annotation_listener.xml');

            if ($this->isConfigEnabled($container, $config['alternate'])) {
                $container->setParameter($this->getAlias() . '.alternate', $config['alternate']);
                $loader->load('alternate_listener.xml');
            }
        }

        if (interface_exists(MessageBusInterface::class)) {
            $loader->load('messenger.xml');
        }

        $generator = $container->setAlias('presta_sitemap.generator', $config['generator']);
        $generator->setPublic(true);

        $dumper = $container->setAlias('presta_sitemap.dumper', $config['dumper']);
        $dumper->setPublic(true);
    }
}
