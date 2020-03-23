<?php

/**
 * This file is part of the PrestaSitemapBundle package.
 *
 * (c) PrestaConcept <www.prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle\Tests\Unit\Sitemap;

use PHPUnit\Framework\TestCase;
use Presta\SitemapBundle\Sitemap;

/**
 * @author David Epely <depely@prestaconcept.net>
 */
class UrlsetTest extends TestCase
{
    protected $urlset;

    public function setUp()
    {
        $this->urlset = new Sitemap\Urlset('http://acme.com/sitemap.default.xml');
    }

    public function testAddUrl()
    {
        $failed = false;
        try {
            $this->urlset->addUrl(new Sitemap\Url\UrlConcrete('http://acme.com/'));
        } catch (\RuntimeException $e) {
            $failed = true;
        }

        self::assertFalse($failed, 'An exception must not be thrown');
    }

    public function testToXml()
    {
        $this->urlset->addUrl(new Sitemap\Url\UrlConcrete('http://acme.com/'));

        $xml = new \DOMDocument;
        $xml->loadXML($this->urlset->toXml());

        self::assertEquals(1, $xml->getElementsByTagName('url')->length);
    }
}
