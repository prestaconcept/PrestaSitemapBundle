<?php

/*
 * This file is part of the prestaSitemapPlugin package.
 * (c) David Epely <depely@prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle,
    Symfony\Component\DependencyInjection\ContainerBuilder,
    Presta\SitemapBundle\DependencyInjection\Compiler\AddSitemapListenersPass;

/**
 * Bundle that provides tools to render application sitemap according to 
 * sitemap protocol. @see http://www.sitemaps.org/ 
 * @see README.md for basic usage
 * 
 * @author depely 
 */
class PrestaSitemapBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new AddSitemapListenersPass());
    }
}
