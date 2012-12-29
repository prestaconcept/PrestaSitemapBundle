<?php

namespace Presta\SitemapBundle\Test\Sitemap\Url;

use Presta\SitemapBundle\Sitemap;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;

/**
 * @author  David Epely
 */
class UrlConcreteTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider testToXmlProvider
     */
    public function testToXml($expectedXml, $loc, $lastmod = null, $changefreq = null, $priority = null)
    {
        $url = new UrlConcrete($loc, $lastmod, $changefreq, $priority);
        $this->assertEquals($expectedXml, $url->toXml());
    }

    public function testToXmlProvider()
    {
        return array(
            array('<url><loc>http://example.com/</loc></url>', 'http://example.com/'),
            array('<url><loc>http://example.com/abcd</loc></url>', 'http://example.com/abcd'),
            array('<url><loc>http://example.com/abcd/?a=1&amp;b=cdf</loc></url>', 'http://example.com/abcd/?a=1&b=cdf'),
            array('<url><loc>http://example.com/</loc><lastmod>2012-12-29T10:39:12+01:00</lastmod></url>', 'http://example.com/', new \DateTime('2012-12-29 10:39:12')),
            array('<url><loc>http://example.com/</loc><changefreq>always</changefreq></url>', 'http://example.com/', null, 'always'),
            array('<url><loc>http://example.com/</loc><changefreq>hourly</changefreq></url>', 'http://example.com/', null, 'hourly'),
            array('<url><loc>http://example.com/</loc><changefreq>weekly</changefreq></url>', 'http://example.com/', null, 'weekly'),
            array('<url><loc>http://example.com/</loc><changefreq>monthly</changefreq></url>', 'http://example.com/', null, 'monthly'),
            array('<url><loc>http://example.com/</loc><changefreq>yearly</changefreq></url>', 'http://example.com/', null, 'yearly'),
            array('<url><loc>http://example.com/</loc><changefreq>never</changefreq></url>', 'http://example.com/', null, 'never'),
            array('<url><loc>http://example.com/</loc><changefreq>daily</changefreq></url>', 'http://example.com/', null, 'daily'),
            array('<url><loc>http://example.com/</loc><priority>0.1</priority></url>', 'http://example.com/', null, null, 0.1),
            array('<url><loc>http://example.com/</loc><priority>0.5</priority></url>', 'http://example.com/', null, null, 0.5),
            array('<url><loc>http://example.com/</loc><priority>1.0</priority></url>', 'http://example.com/', null, null, 1),
            array('<url><loc>http://example.com/abcd/?a=1&amp;b=cdf&amp;ghj=ijklmn</loc><lastmod>2012-01-01T00:00:00+01:00</lastmod><changefreq>daily</changefreq><priority>0.7</priority></url>', 'http://example.com/abcd/?a=1&b=cdf&ghj=ijklmn', new \DateTime('2012-1-1 00:00:00'), 'daily', 0.7),
            array('<url><loc>http://example.com/abcd/?a=1&amp;b=cdf&amp;ghj=ijklmn</loc><changefreq>daily</changefreq><priority>0.7</priority></url>', 'http://example.com/abcd/?a=1&b=cdf&ghj=ijklmn', null, 'daily', 0.7),
        );
    }
}
