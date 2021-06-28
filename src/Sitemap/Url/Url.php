<?php

/*
 * This file is part of the PrestaSitemapBundle package.
 *
 * (c) PrestaConcept <https://prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle\Sitemap\Url;

/**
 * Representation of an Url in urlset
 */
interface Url
{
    /**
     * Render element as xml
     *
     * @return string
     */
    public function toXml(): string;

    /**
     * List of used namespaces.
     *
     * @return array<string, string>
     */
    public function getCustomNamespaces(): array;
}
