<?php

/*
 * This file is part of the PrestaSitemapBundle package.
 *
 * (c) PrestaConcept <https://prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle\Service;

use Presta\SitemapBundle\Sitemap\XmlConstraint;

/**
 * Interface for class that intend to generate a sitemap.
 */
interface GeneratorInterface extends UrlContainerInterface
{
    /**
     * Generate sitemap section.
     *
     * @param string $name The section name (or "root" for all sections)
     *
     * @return XmlConstraint|null The generated XML (or null if section not found)
     */
    public function fetch(string $name): ?XmlConstraint;
}
