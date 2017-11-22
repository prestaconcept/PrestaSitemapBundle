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

use DateTime;
use Presta\SitemapBundle\Sitemap\Url\Url;

/**
 * Representation of url list
 *
 * @author depely
 */
class Urlset extends XmlConstraint
{
    const TAG = 'sitemap';

    /**
     * @var string
     */
    protected $loc;

    /**
     * @var DateTime
     */
    protected $lastmod;

    /**
     * @var string
     */
    protected $urlsXml = '';

    /**
     * @var array
     */
    protected $customNamespaces = array();

    /**
     * @param string         $loc
     * @param DateTime|null $lastmod
     */
    public function __construct($loc, DateTime $lastmod = null)
    {
        $this->loc = $loc;
        $this->lastmod = $lastmod ? $lastmod : new DateTime();
    }

    /**
     * @return string
     */
    public function getLoc()
    {
        return $this->loc;
    }

    /**
     * @return DateTime
     */
    public function getLastmod()
    {
        return $this->lastmod;
    }

    /**
     * add url to pool and check limits
     *
     * @param Url $url
     *
     * @throws \RuntimeException
     */
    public function addUrl(Url $url)
    {
        if ($this->isFull()) {
            throw new \RuntimeException('The urlset limit has been exceeded');
        }

        $urlXml = $url->toXml();
        $this->appendXML($urlXml);

        //add unknown custom namespaces
        $this->customNamespaces = array_merge($this->customNamespaces, $url->getCustomNamespaces());

        //---------------------
        //Check limits
        if ($this->countItems++ >= self::LIMIT_ITEMS) {
            $this->limitItemsReached = true;
        }

        $urlLength = strlen($urlXml);
        $this->countBytes += $urlLength;

        if ($this->countBytes + $urlLength + strlen($this->getStructureXml()) > self::LIMIT_BYTES) {
            //we suppose the next url is almost the same length and cannot be added
            //plus we keep 500kB (@see self::LIMIT_BYTES)
            //... beware of numerous images set in url
            $this->limitBytesReached = true;
        }
        //---------------------
    }

    /**
     * Appends URL's XML to internal string buffer
     *
     * @param string $urlXml
     */
    protected function appendXML($urlXml)
    {
        $this->urlsXml .= $urlXml;
    }

    /**
     * get the xml structure of the current urlset
     *
     * @return string
     */
    protected function getStructureXml()
    {
        $struct = '<?xml version="1.0" encoding="UTF-8"?>';
        $struct .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" NAMESPACES>URLS</urlset>';

        $namespaces = '';
        foreach ($this->customNamespaces as $key => $location) {
            $namespaces .= ' xmlns:' . $key . '="' . $location . '"';
        }

        $struct = str_replace('NAMESPACES', $namespaces, $struct);

        return $struct;
    }

    /**
     * @inheritdoc
     */
    public function toXml()
    {
        return str_replace('URLS', $this->urlsXml, $this->getStructureXml());
    }
}
