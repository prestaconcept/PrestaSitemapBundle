<?php

/*
 * This file is part of the PrestaSitemapBundle package.
 *
 * (c) PrestaConcept <https://prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle\Sitemap;

use DateTimeInterface;
use Presta\SitemapBundle\Sitemap\Url\Url;

/**
 * Url set containing urls.
 *
 * https://www.sitemaps.org/protocol.html
 * https://developers.google.com/search/docs/advanced/sitemaps/large-sitemaps
 */
class Urlset extends XmlConstraint
{
    public const TAG = 'sitemap';

    /**
     * @var string
     */
    protected $loc;

    /**
     * @var DateTimeInterface
     */
    protected $lastmod;

    /**
     * @var string
     */
    protected $urlsXml = '';

    /**
     * @var array<string, string>
     */
    protected $customNamespaces = [];

    /**
     * @param string                 $loc
     * @param DateTimeInterface|null $lastmod
     */
    public function __construct(string $loc, DateTimeInterface $lastmod = null)
    {
        $this->loc = $loc;
        $this->lastmod = $lastmod ?? new \DateTimeImmutable();
    }

    /**
     * @return string
     */
    public function getLoc(): string
    {
        return $this->loc;
    }

    /**
     * @return DateTimeInterface
     */
    public function getLastmod(): DateTimeInterface
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
    public function addUrl(Url $url): void
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
    protected function appendXML(string $urlXml): void
    {
        $this->urlsXml .= $urlXml;
    }

    /**
     * get the xml structure of the current urlset
     *
     * @return string
     */
    protected function getStructureXml(): string
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
    public function toXml(): string
    {
        return str_replace('URLS', $this->urlsXml, $this->getStructureXml());
    }
}
