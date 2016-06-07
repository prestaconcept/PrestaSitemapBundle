<?php

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
