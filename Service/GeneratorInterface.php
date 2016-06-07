<?php

namespace Presta\SitemapBundle\Service;

use Presta\SitemapBundle\Sitemap\Sitemapindex;
use Presta\SitemapBundle\Sitemap\Urlset;

/**
 * Interface for class that intend to generate a sitemap.
 *
 * @author Yann Eugoné <yeugone@prestaconcept.net>
 */
interface GeneratorInterface extends UrlContainerInterface
{
    /**
     * Generate all datas and store in cache if it is possible
     */
    public function generate();

    /**
     * Get eventual cached data or generate whole sitemap
     *
     * @param string $name
     *
     * @return Sitemapindex|Urlset|null
     */
    public function fetch($name);
}
