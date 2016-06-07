<?php

/*
 * This file is part of the prestaSitemapPlugin package.
 * (c) David Epely <depely@prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Presta\SitemapBundle\DependencyInjection\Compiler\AddSitemapListenersPass;

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
        $container->addCompilerPass(new AddSitemapListenersPass(), PassConfig::TYPE_OPTIMIZE);
    }
}
