<?php

/*
 * This file is part of the PrestaSitemapBundle package.
 *
 * (c) PrestaConcept <https://prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle\Tests\Unit\Sitemap;

use PHPUnit\Framework\TestCase;
use Presta\SitemapBundle\Sitemap;

class SitemapindexTest extends TestCase
{
    public function testAddSitemap(): void
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

    public function testGetSitemapXml(): void
    {
        $today          = new \DateTime();
        $loc            = 'http://acme.com/';
        $sitemapindex   = new Sitemap\Sitemapindex();

        $getSitemapXmlMethod = self::getMethod($sitemapindex, 'getSitemapXml');

        self::assertXmlStringEqualsXmlString(
            '<sitemap><loc>' . $loc . '</loc><lastmod>' . $today->format('c') . '</lastmod></sitemap>',
            $getSitemapXmlMethod->invoke($sitemapindex, new Sitemap\Urlset($loc, $today)),
            '->getSitemapXml() render xml'
        );
    }

    public function testToXml(): void
    {
        $sitemapindex = new Sitemap\Sitemapindex();
        $xml = $sitemapindex->toXml();
        self::assertXmlStringEqualsXmlString(
            '<?xml version="1.0" encoding="UTF-8"?><sitemapindex xmlns:xsi="https://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="https://www.sitemaps.org/schemas/sitemap/0.9 https://www.sitemaps.org/schemas/sitemap/0.9/siteindex.xsd" xmlns="https://www.sitemaps.org/schemas/sitemap/0.9"></sitemapindex>',
            $xml
        );
    }

    /**
     * get accessible method that was private or protected
     *
     * @param mixed  $obj - classname or instance
     * @param string $name
     */
    protected static function getMethod($obj, $name): \ReflectionMethod
    {
        $method = new \ReflectionMethod($obj, $name);
        $method->setAccessible(true);
        return $method;
    }
}
