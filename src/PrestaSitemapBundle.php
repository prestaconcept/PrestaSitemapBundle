<?php

/*
 * This file is part of the PrestaSitemapBundle package.
 *
 * (c) PrestaConcept <https://prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle;

use Presta\SitemapBundle\DependencyInjection\Compiler\EventAliasMappingPass;
use Presta\SitemapBundle\Event\SitemapAddUrlEvent;
use Presta\SitemapBundle\Event\SitemapPopulateEvent;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\EventDispatcher\DependencyInjection\AddEventAliasesPass;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Render/Dump Symfony application sitemap in respect of sitemap protocol.
 *
 * https://www.sitemaps.org/
 */
class PrestaSitemapBundle extends Bundle
{
    /**
     * @inheritdoc
     *
     * @return void
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new AddEventAliasesPass([
            SitemapAddUrlEvent::class => SitemapAddUrlEvent::NAME,
            SitemapPopulateEvent::class => SitemapPopulateEvent::ON_SITEMAP_POPULATE,
        ]));
    }

    /**
     * @inheritDoc
     */
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
