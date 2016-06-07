<?php

/*
 * This file is part of the prestaSitemapPlugin package.
 * (c) David Epely <depely@prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle\DependencyInjection;

use Presta\SitemapBundle\Sitemap\XmlConstraint;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 */
class Configuration implements ConfigurationInterface
{
    const DEFAULT_FILENAME = 'sitemap';

    /**
     * @inheritDoc
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('presta_sitemap');

        $rootNode->children()
            ->scalarNode('generator')->defaultValue('presta_sitemap.generator_default')->end()
            ->scalarNode('dumper')->defaultValue('presta_sitemap.dumper_default')->end()
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
            ->scalarNode('items_by_set')
                // Add one to the limit items value because it's an
                // index value (not a quantity)
                ->defaultValue(XmlConstraint::LIMIT_ITEMS + 1)
                ->info('The maximum number of items allowed in single sitemap.')
            ->end()
            ->scalarNode('route_annotation_listener')->defaultTrue()->end()
        ;

        return $treeBuilder;
    }
}
