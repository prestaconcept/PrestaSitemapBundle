<?php

/*
 * This file is part of the prestaSitemapPlugin package.
 * (c) David Epely <depely@prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    const DEFAULT_FILENAME = 'sitemap';

    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('presta_sitemap');

        $rootNode->children()
                    ->scalarNode('timetolive')
                        ->defaultValue('3600')
                    ->end()
                    ->scalarNode('sitemap_file_prefix')
                        ->defaultValue(self::DEFAULT_FILENAME)
                        ->info('Sets sitemap filename prefix defaults to "sitemap" -> sitemap.xml (for index); sitemap.<section>.xml(.gz) (for sitemaps)')
                    ->end()
                    ->scalarNode('dumper_base_url')
                        ->defaultValue('http://localhost/')
                        ->info('Deprecated: please use host option in command. Used for dumper command. Default host to use if host argument is missing')
                    ->end()
                    ->scalarNode('route_annotation_listener')->defaultTrue()->end()
        ;

        return $treeBuilder;
    }
}
