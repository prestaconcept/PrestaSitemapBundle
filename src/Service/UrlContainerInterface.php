<?php

/**
 * This file is part of the PrestaSitemapBundle package.
 *
 * (c) PrestaConcept <www.prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle\Service;

use Presta\SitemapBundle\Sitemap\Url\Url;

/**
 * Interface for class that intend contain urls.
 *
 * @author Yann Eugoné <yeugone@prestaconcept.net>
 */
interface UrlContainerInterface
{
    /**
     * Add an Url to an Urlset
     *
     * section is helpfull for partial cache invalidation
     *
     * @param Url    $url
     * @param string $section
     *
     * @throws \RuntimeException
     */
    public function addUrl(Url $url, $section);
}
