<?php

/*
 * This file is part of the PrestaSitemapBundle package.
 *
 * (c) PrestaConcept <https://prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle\Tests\Unit\Sitemap\Url;

use PHPUnit\Framework\TestCase;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;

class UrlConcreteTest extends TestCase
{
    /**
     * @dataProvider toXmlProvider
     */
    public function testToXml($expectedXml, $loc, $lastmod = null, $changefreq = null, $priority = null): void
    {
        $url = new UrlConcrete($loc, $lastmod, $changefreq, $priority);
        self::assertEquals($expectedXml, $url->toXml());
    }

    public function toXmlProvider(): array
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

    /**
     * @dataProvider setPriorityProvider
     */
    public function testSetPriority($assigned, ?float $expected): void
    {
        $url = new UrlConcrete('http://example.com');
        $url->setPriority($assigned);
        self::assertSame($expected, $url->getPriority());
    }

    public function setPriorityProvider(): \Generator
    {
        yield [null, null];
        yield [0, 0.0];
        yield ['0', 0.0];
        yield [0.555, 0.6];
        yield ['0.5', 0.5];
        yield [1, 1.0];
        yield [1.00, 1.0];
    }

    /**
     * @dataProvider setInvalidPriorityProvider
     */
    public function testSetInvalidPriority($value): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            "The value \"$value\" is not supported by the option priority, it must be a numeric between 0.0 and 1.0." .
            " See https://www.sitemaps.org/protocol.html#xmlTagDefinitions"
        );

        $url = new UrlConcrete('http://example.com');
        $url->setPriority($value);
    }

    public function setInvalidPriorityProvider(): \Generator
    {
        yield [true];
        yield [-1];
        yield [-0.01];
        yield [-2];
        yield [-1.01];
    }
}
