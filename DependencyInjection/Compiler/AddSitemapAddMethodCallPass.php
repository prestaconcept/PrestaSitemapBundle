<?php

/**
 * This file is part of the PrestaSitemapBundle package.
 *
 * (c) PrestaConcept <www.prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Dynamically set the cache pool service by its name configured in 'presta_sitemap.cache.pool'
 */
class AddSitemapAddMethodCallPass implements CompilerPassInterface
{
    /**
     * @inheritdoc
     */
    public function process(ContainerBuilder $container)
    {
        if ($container->hasParameter('presta_sitemap.cache.pool')) {
            $cachePool = $container->getParameter('presta_sitemap.cache.pool');
            if (!is_null($cachePool)) {
                $definition = $container->getDefinition('presta_sitemap.generator_default');
                $reference = new Reference($cachePool);
                $definition->addMethodCall('setCachePool', array($reference));
            }
        }
    }
}
