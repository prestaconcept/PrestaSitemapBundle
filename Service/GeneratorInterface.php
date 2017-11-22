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

use Presta\SitemapBundle\Sitemap\XmlConstraint;

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
     * @return XmlConstraint|null
     */
    public function fetch($name);
}
