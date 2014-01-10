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
 * Representation of sitemap (urlset) list
 *
 * @author  David Epely
 */
class Sitemapindex extends XmlConstraint
{
    protected $sitemapsXml = '';

    public function addSitemap(Urlset $urlset)
    {
        if ($this->isFull()) {
            throw new \RuntimeException('The sitemapindex limit has been exceeded');
        }

        $sitemapXml = $this->getSitemapXml($urlset);
        $this->sitemapsXml .= $sitemapXml;

        //---------------------
        //Check limits
        if ($this->countItems++ >= self::LIMIT_ITEMS) {
            $this->limitItemsReached = true;
        }

        $sitemapLength = strlen($sitemapXml);
        $this->countBytes += $sitemapLength;

        if ($this->countBytes + $sitemapLength + strlen($this->getStructureXml()) > self::LIMIT_BYTES) {
            //we suppose the next sitemap is almost the same length and cannot be added
            //plus we keep 500kB (@see self::LIMIT_BYTES)
            $this->limitBytesReached = true;
        }
        //---------------------
    }

    /**
     * Render urlset as sitemap in xml
     *
     * @param Urlset $urlset
     * @return string
     */
    protected function getSitemapXml(Urlset $urlset)
    {
        return '<sitemap><loc>' . $urlset->getLoc()
                . '</loc><lastmod>' . $urlset->getLastmod()->format('c')
                . '</lastmod></sitemap>';
    }

    /**
     * sitemindex xml structure
     *
     * @return string
     */
    protected function getStructureXml()
    {
        $struct = '<?xml version="1.0" encoding="UTF-8"?>';
        $struct .= '<sitemapindex xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"';
        $struct .= ' xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/siteindex.xsd"';
        $struct .= ' xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">SITEMAPS</sitemapindex>';

        return $struct;
    }

    /**
     * @see parent::toXml()
     */
    public function toXml()
    {
        $xml = $this->getStructureXml();

        return str_replace('SITEMAPS', $this->sitemapsXml, $xml);
    }
}
