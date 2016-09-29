<?php

/**
 * This file is part of the PrestaSitemapBundle package.
 *
 * (c) PrestaConcept <www.prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle\Test\Sitemap;

use Presta\SitemapBundle\Sitemap;

/**
 * @author David Epely <depely@prestaconcept.net>
 */
class UrlsetTest extends \PHPUnit_Framework_TestCase
{
    protected $urlset;

    public function setUp()
    {
        $this->urlset = new Sitemap\Urlset('http://acme.com/sitemap.default.xml');
    }

    public function testAddUrl()
    {
        try {
            $this->urlset->addUrl(new Sitemap\Url\UrlConcrete('http://acme.com/'));
        } catch (\RuntimeException $e) {
            $this->fail('An exception must not be thrown');
        }
    }

    public function testToXml()
    {
        $this->urlset->addUrl(new Sitemap\Url\UrlConcrete('http://acme.com/'));

        $xml = new \DOMDocument;
        $xml->loadXML($this->urlset->toXml());

        $this->assertEquals(1, $xml->getElementsByTagName('url')->length);
    }
}
