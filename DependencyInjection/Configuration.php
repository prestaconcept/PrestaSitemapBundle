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
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
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
        if (method_exists(TreeBuilder::class, 'getRootNode')) {
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
                ->integerNode('timetolive')
                    ->defaultValue(3600)
                ->end()
                ->scalarNode('sitemap_file_prefix')
                    ->defaultValue(self::DEFAULT_FILENAME)
                    ->info('Sets sitemap filename prefix defaults to "sitemap" -> sitemap.xml (for index); sitemap.<section>.xml(.gz) (for sitemaps)')
                ->end()
                ->integerNode('items_by_set')
                    // Add one to the limit items value because it's an
                    // index value (not a quantity)
                    ->defaultValue(XmlConstraint::LIMIT_ITEMS + 1)
                    ->info('The maximum number of items allowed in single sitemap.')
                ->end()
                ->scalarNode('route_annotation_listener')->defaultTrue()->end()
                ->scalarNode('dump_directory')
                    ->info(
                        'The directory to which the sitemap will be dumped. '.
                        'It can be either absolute, or relative (to the place where the command will be triggered). '.
                        'Default to Symfony\'s public dir.'
                    )
                    ->defaultValue(
                        '%kernel.project_dir%/'.(version_compare(Kernel::VERSION, '4.0') >= 0 ? 'public' : 'web')
                    )
                ->end()
                ->arrayNode('defaults')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('priority')->defaultValue(0.5)->end()
                        ->scalarNode('changefreq')->defaultValue(UrlConcrete::CHANGEFREQ_DAILY)->end()
                        ->scalarNode('lastmod')->defaultValue('now')->end()
                    ->end()
                ->end()
                ->scalarNode('default_section')
                    ->defaultValue('default')
                    ->info('The default section in which static routes are registered.')
                ->end()
            ->end()
        ;

        $this->addAlternateSection($rootNode);

        return $treeBuilder;
    }

    private function addAlternateSection(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->arrayNode('alternate')
                    ->info(
                        'Automatically generate alternate (hreflang) urls with static routes.' .
                       ' Requires route_annotation_listener config to be enabled.'
                    )
                    ->canBeEnabled()
                    ->children()
                        ->scalarNode('default_locale')
                            ->defaultValue('en')
                            ->info('The default locale of your routes.')
                        ->end()
                        ->arrayNode('locales')
                            ->defaultValue(['en'])
                            ->beforeNormalization()
                            ->ifString()
                                ->then(function ($v) { return preg_split('/\s*,\s*/', $v); })
                            ->end()
                            ->prototype('scalar')->end()
                            ->info('List of supported locales of your routes.')
                        ->end()
                        ->enumNode('i18n')
                            ->defaultValue('symfony')
                            ->values(['symfony', 'jms'])
                            ->info('Strategy used to create your i18n routes.')
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
