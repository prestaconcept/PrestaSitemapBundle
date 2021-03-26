<?php

/**
 * This file is part of the PrestaSitemapBundle package.
 *
 * (c) PrestaConcept <www.prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle\Sitemap;

/**
 * Xml requirements for sitemap protocol
 *
 * @see http://www.sitemaps.org/protocol.html
 *
 * @author depely
 */
abstract class XmlConstraint implements \Countable
{
    const LIMIT_ITEMS = 49999;
    const LIMIT_BYTES = 50000000; // 52,428,800 bytes - 2,428,800

    /**
     * @var bool
     */
    protected $limitItemsReached = false;

    /**
     * @var bool
     */
    protected $limitBytesReached = false;

    /**
     * @var int
     */
    protected $countBytes = 0;

    /**
     * @var int
     */
    protected $countItems = 0;

    /**
     * @return bool
     */
    public function isFull()
    {
        return $this->limitItemsReached || $this->limitBytesReached;
    }

    /**
     * @inheritdoc
     */
    public function count()
    {
        return $this->countItems;
    }

    /**
     * Render full and valid xml
     */
    abstract public function toXml();
}
