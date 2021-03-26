<?php

/**
 * This file is part of the PrestaSitemapBundle package.
 *
 * (c) PrestaConcept <www.prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle\Sitemap\Url;

/**
 * Representation of an Url in urlset
 *
 * @author depely
 */
interface Url
{
    /**
     * Render element as xml
     *
     * @return string
     */
    public function toXml();

    /**
     * List of used namespaces
     *
     * @return array - [{ns} => {location}]
     */
    public function getCustomNamespaces();
}
