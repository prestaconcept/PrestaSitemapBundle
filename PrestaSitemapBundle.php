<?php

/**
 * This file is part of the PrestaSitemapBundle package.
 *
 * (c) PrestaConcept <www.prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle;

use Presta\SitemapBundle\DependencyInjection\Compiler\AddSitemapAddMethodCallPass;
use Presta\SitemapBundle\DependencyInjection\Compiler\AddSitemapListenersPass;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Bundle that provides tools to render application sitemap according to
 * sitemap protocol. @see http://www.sitemaps.org/
 * @see README.md for basic usage
 *
 * @author depely
 */
class PrestaSitemapBundle extends Bundle
{
    /**
     * @inheritdoc
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new AddSitemapListenersPass(), PassConfig::TYPE_OPTIMIZE);
        $container->addCompilerPass(new AddSitemapAddMethodCallPass(), PassConfig::TYPE_OPTIMIZE);
    }
}
