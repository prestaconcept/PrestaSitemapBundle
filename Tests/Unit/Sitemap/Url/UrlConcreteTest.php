<?php

/**
 * This file is part of the PrestaSitemapBundle package.
 *
 * (c) PrestaConcept <www.prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle\Tests\Unit\Sitemap\Url;

use PHPUnit\Framework\TestCase;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;

/**
 * @author David Epely <depely@prestaconcept.net>
 */
class UrlConcreteTest extends TestCase
{
    /**
     * @dataProvider toXmlProvider
     */
    public function testToXml($expectedXml, $loc, $lastmod = null, $changefreq = null, $priority = null)
    {
        $url = new UrlConcrete($loc, $lastmod, $changefreq, $priority);
        self::assertEquals($expectedXml, $url->toXml());
    }

    public function toXmlProvider()
    {
        return [
            ['<url><loc>http://example.com/</loc></url>', 'http://example.com/'],
            ['<url><loc>http://example.com/abcd</loc></url>', 'http://example.com/abcd'],
            ['<url><loc>http://example.com/abcd/?a=1&amp;b=cdf</loc></url>', 'http://example.com/abcd/?a=1&b=cdf'],
            [
                '<url><loc>http://example.com/</loc><lastmod>2012-12-29T10:39:12+00:00</lastmod></url>',
                'http://example.com/',
                new \DateTime('2012-12-29 10:39:12', new \DateTimeZone('Europe/London'))
            ],
            [
                '<url><loc>http://example.com/</loc><changefreq>always</changefreq></url>',
                'http://example.com/',
                null,
                'always'
            ],
            [
                '<url><loc>http://example.com/</loc><changefreq>hourly</changefreq></url>',
                'http://example.com/',
                null,
                'hourly'
            ],
            [
                '<url><loc>http://example.com/</loc><changefreq>weekly</changefreq></url>',
                'http://example.com/',
                null,
                'weekly'
            ],
            [
                '<url><loc>http://example.com/</loc><changefreq>monthly</changefreq></url>',
                'http://example.com/',
                null,
                'monthly'
            ],
            [
                '<url><loc>http://example.com/</loc><changefreq>yearly</changefreq></url>',
                'http://example.com/',
                null,
                'yearly'
            ],
            [
                '<url><loc>http://example.com/</loc><changefreq>never</changefreq></url>',
                'http://example.com/',
                null,
                'never'
            ],
            [
                '<url><loc>http://example.com/</loc><changefreq>daily</changefreq></url>',
                'http://example.com/',
                null,
                'daily'
            ],
            [
                '<url><loc>http://example.com/</loc><priority>0.1</priority></url>',
                'http://example.com/',
                null,
                null,
                0.1
            ],
            [
                '<url><loc>http://example.com/</loc><priority>0.5</priority></url>',
                'http://example.com/',
                null,
                null,
                0.5
            ],
            ['<url><loc>http://example.com/</loc><priority>1.0</priority></url>', 'http://example.com/', null, null, 1],
            [
                '<url><loc>http://example.com/abcd/?a=1&amp;b=cdf&amp;ghj=ijklmn</loc><lastmod>2012-01-01T00:00:00+00:00</lastmod><changefreq>daily</changefreq><priority>0.7</priority></url>',
                'http://example.com/abcd/?a=1&b=cdf&ghj=ijklmn',
                new \DateTime('2012-1-1 00:00:00', new \DateTimeZone('Europe/London')),
                'daily',
                0.7
            ],
            [
                '<url><loc>http://example.com/abcd/?a=1&amp;b=cdf&amp;ghj=ijklmn</loc><changefreq>daily</changefreq><priority>0.7</priority></url>',
                'http://example.com/abcd/?a=1&b=cdf&ghj=ijklmn',
                null,
                'daily',
                0.7
            ],
        ];
    }
}
