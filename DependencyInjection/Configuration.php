<?php

/**
 * This file is part of the PrestaSitemapBundle package.
 *
 * (c) PrestaConcept <www.prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle\DependencyInjection;

use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;
use Presta\SitemapBundle\Sitemap\XmlConstraint;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\HttpKernel\Kernel;

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
        if (version_compare(Kernel::VERSION, '4.2') >= 0) {
            $treeBuilder = new TreeBuilder('presta_sitemap');
            $rootNode = $treeBuilder->getRootNode();
        } else {
            $treeBuilder = new TreeBuilder();
            $rootNode = $treeBuilder->root('presta_sitemap');
        }

        $rootNode
            ->children()
                ->scalarNode('generator')->defaultValue('presta_sitemap.generator_default')->end()
                ->scalarNode('dumper')->defaultValue('presta_sitemap.dumper_default')->end()
                ->scalarNode('timetolive')
                    ->defaultValue('3600')
                ->end()
                ->scalarNode('sitemap_file_prefix')
                    ->defaultValue(self::DEFAULT_FILENAME)
                    ->info('Sets sitemap filename prefix defaults to "sitemap" -> sitemap.xml (for index); sitemap.<section>.xml(.gz) (for sitemaps)')
                ->end()
                ->scalarNode('items_by_set')
                    // Add one to the limit items value because it's an
                    // index value (not a quantity)
                    ->defaultValue(XmlConstraint::LIMIT_ITEMS + 1)
                    ->info('The maximum number of items allowed in single sitemap.')
                ->end()
                ->scalarNode('route_annotation_listener')->defaultTrue()->end()
                ->arrayNode('defaults')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('priority')->defaultValue(1)->end()
                        ->scalarNode('changefreq')->defaultValue(UrlConcrete::CHANGEFREQ_DAILY)->end()
                        ->scalarNode('lastmod')->defaultValue('now')->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
