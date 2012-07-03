<?php

/*
 * This file is part of the prestaSitemapPlugin package.
 * (c) David Epely <depely@prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle\Sitemap;

/**
 * Xml requirements for sitemap protocol
 * @see http://www.sitemaps.org/protocol.html
 *
 * @author depely
 */
abstract class XmlConstraint
{
    const LIMIT_ITEMS = 49999;
    const LIMIT_BYTES = 10000000; // 10,485,760 bytes - 485,760

    protected $limitItemsReached = false;
    protected $limitBytesReached = false;
    protected $countBytes = 0;
    protected $countItems = 0;

    /**
     * @return bool
     */
    public function isFull()
    {
        return $this->limitItemsReached || $this->limitBytesReached;
    }

    /**
     * Render full and valid xml 
     */
    abstract function toXml();
}
