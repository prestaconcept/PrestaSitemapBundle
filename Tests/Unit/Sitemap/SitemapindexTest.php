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
 * Manage sitemaps listing
 *
 * @author David Epely <depely@prestaconcept.net>
 */
class SitemapindexTest extends TestCase
{
    public function testAddSitemap()
    {
        $sitemapindex = new Sitemap\Sitemapindex();

        $failed = false;
        try {
            $sitemapindex->addSitemap(new Sitemap\Urlset('http://acme.com'));
        } catch (\RuntimeException $e) {
            $failed = true;
        }
        self::assertFalse($failed, 'An exception must not be thrown');
    }

    public function testGetSitemapXml()
    {
        $today          = new \DateTime;
        $loc            = 'http://acme.com/';
        $sitemapindex   = new Sitemap\Sitemapindex();

        $getSitemapXmlMethod = self::getMethod($sitemapindex, 'getSitemapXml');

        self::assertXmlStringEqualsXmlString(
            '<sitemap><loc>' . $loc . '</loc><lastmod>' . $today->format('c') . '</lastmod></sitemap>',
            $getSitemapXmlMethod->invoke($sitemapindex, new Sitemap\Urlset($loc, $today)),
            '->getSitemapXml() render xml'
        );
    }

    public function testToXml()
    {
        $sitemapindex   = new Sitemap\Sitemapindex();
        $xml = $sitemapindex->toXml();
        self::assertXmlStringEqualsXmlString(
            '<?xml version="1.0" encoding="UTF-8"?><sitemapindex xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/siteindex.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></sitemapindex>',
            $xml
        );
    }

    /**
     * get accessible method that was private or protected
     *
     * @param mixed $obj - classname or instance
     * @param type $name
     * @return \ReflectionMethod
     */
    protected static function getMethod($obj, $name)
    {
        $method = new \ReflectionMethod($obj, $name);
        $method->setAccessible(true);
        return $method;
    }
}
