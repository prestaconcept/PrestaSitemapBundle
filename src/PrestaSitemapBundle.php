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

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Render/Dump Symfony application sitemap in respect of sitemap protocol.
 *
 * https://www.sitemaps.org/
 */
class PrestaSitemapBundle extends Bundle
{
    /**
     * @inheritDoc
     */
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
