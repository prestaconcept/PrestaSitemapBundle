<?php

/**
 * This file is part of the PrestaSitemapBundle package.
 *
 * (c) PrestaConcept <www.prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle\Test\Sitemap\Url;

use PHPUnit\Framework\TestCase;
use Presta\SitemapBundle\Exception\GoogleNewsUrlException;
use Presta\SitemapBundle\Service\Generator;
use Presta\SitemapBundle\Sitemap\Url\GoogleNewsUrlDecorator;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;
use Presta\SitemapBundle\Sitemap\Url\Url;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Tests the GoogleNewsUrlDecorator
 *
 * @author Christoph Foehrdes
 */
class GoogleNewsUrlDecoratorTest extends TestCase
{
    /**
     * Tests if the news specific tags can be found.
     */
    public function testCountNamespaces()
    {
        $url = $this->createExampleUrl();
        $dom = new \DOMDocument();
        $dom->loadXML($this->generateXml($url));

        $newsTags = $dom->getElementsByTagNameNS('http://www.google.com/schemas/sitemap-news/0.9', '*');

        $this->assertEquals(6, $newsTags->length, 'Could not find news specific tags');
    }

    /**
     * Tests the default W3C format.
     */
    public function testDefaultDateFormat()
    {
        $date = new \DateTime('2013-11-05 10:30:55');

        // test default W3C format
        $url = $this->createExampleUrl();
        $url->setPublicationDate($date);
        $dom = new \DOMDocument();
        $dom->loadXML($this->generateXml($url));

        $dateNodes = $dom->getElementsByTagNameNS('http://www.google.com/schemas/sitemap-news/0.9', 'publication_date');
        $this->assertEquals(1, $dateNodes->length, 'Could not find news:publication_date tag');
        $this->assertEquals($date->format(\DateTime::W3C), $dateNodes->item(0)->textContent, 'Date was not formatted properly');
    }

    /**
     * Test the custom date only format property.
     */
    public function testCustomDateFormat()
    {
        $date = new \DateTime('2013-11-05 10:30:55');

        // test date only format
        $url = $this->createExampleUrl();
        $url->setPublicationDate($date);
        $url->setPublicationDateFormat(GoogleNewsUrlDecorator::DATE_FORMAT_DATE);
        $dom = new \DOMDocument();
        $dom->loadXML($this->generateXml($url));

        $dateNodes = $dom->getElementsByTagNameNS('http://www.google.com/schemas/sitemap-news/0.9', 'publication_date');
        $this->assertEquals(1, $dateNodes->length, 'Could not find news:publication_date tag');
        $this->assertEquals($date->format('Y-m-d'), $dateNodes->item(0)->textContent, 'Date was not formatted properly');
    }

    /**
     * Tests if the news access property is validated properly.
     */
    public function testAccessPropertyValidation()
    {
        $url = $this->createExampleUrl();

        $failed = false;
        try {
            $url->setAccess('invalid-access');
        } catch (GoogleNewsUrlException $e) {
            $failed = true;
        }
        $this->assertTrue($failed, 'Setting an invalid access string did not fail');

        $failed = false;
        try {
            $url->setAccess(GoogleNewsUrlDecorator::ACCESS_REGISTRATION);
        } catch (GoogleNewsUrlException $e) {
            $failed = true;
        }
        $this->assertFalse($failed, 'Setting a valid access failed');

        $dom = new \DOMDocument();
        $dom->loadXML($this->generateXml($url));
        $accessNodes = $dom->getElementsByTagNameNS('http://www.google.com/schemas/sitemap-news/0.9', 'access');
        $this->assertEquals(1, $accessNodes->length, 'Could not find news:access tag');
        $this->assertEquals('Registration', $accessNodes->item(0)->textContent, 'Acces tag did not contain the right value');
    }

    /**
     * Tests if the news geo location property is validated properly.
     */
    public function testGeoLocationPropertyValidation()
    {
        $url = $this->createExampleUrl();

        $failed = false;
        try {
            $url->setGeoLocations('Somewhere in the world');
        } catch (GoogleNewsUrlException $e) {
            $failed = true;
        }
        $this->assertTrue($failed, 'Setting an invalid location string did not fail');

        $failed = false;
        try {
            $url->setGeoLocations('Hamburg, Germany');
            $url->setGeoLocations('Detroit, Michigan, USA');
        } catch (GoogleNewsUrlException $e) {
            $failed = true;
        }
        $this->assertFalse($failed, 'Setting a valid access failed');

        $dom = new \DOMDocument();
        $dom->loadXML($this->generateXml($url));
        $geoNodes = $dom->getElementsByTagNameNS('http://www.google.com/schemas/sitemap-news/0.9', 'geo_locations');
        $this->assertEquals(1, $geoNodes->length, 'Could not find news:geo_locations tag');
        $this->assertEquals('Detroit, Michigan, USA', $geoNodes->item(0)->textContent, 'Locations tag did not contain the right value');
    }

    /**
     * Tests the limitation of the stock tickers
     */
    public function testStockTickersLimit()
    {
        $url = $this->createExampleUrl();

        $failed = false;
        try {
            $url->setStockTickers(
                array(
                    'NYSE:OWW',
                    'NASDAQ:GTAT',
                    'NYSE:AOL',
                    'NASDAQ:ENDP',
                    'CVE:GTA',
                    'NASDAQ:IMGN'
                )
            );
        } catch (GoogleNewsUrlException $e) {
            $failed = true;
        }
        $this->assertTrue($failed, 'Setting to many stock tickers at once did not fail');

        $failed = false;
        try {
            $url->setStockTickers(
                array(
                    'NYSE:OWW',
                    'NASDAQ:GTAT',
                    'NYSE:AOL',
                    'NASDAQ:ENDP',
                    'CVE:GTA'
                )
            );
        } catch (GoogleNewsUrlException $e) {
            $failed = true;
        }
        $this->assertFalse($failed, 'Setting a valid amount of stock tickers failed');

        $failed = false;
        try {
            $url->addStockTicker('NASDAQ:IMGN');
        } catch (GoogleNewsUrlException $e) {
            $failed = true;
        }
        $this->assertTrue($failed, 'Setting to many stock tickers over the add method did not fail');

        $url->setStockTickers(
            array(
                'NYSE:OWW',
                'NASDAQ:GTAT'
            )
        );
        $dom = new \DOMDocument();
        $dom->loadXML($this->generateXml($url));
        $stockNodes = $dom->getElementsByTagNameNS('http://www.google.com/schemas/sitemap-news/0.9', 'stock_tickers');
        $this->assertEquals(1, $stockNodes->length, 'Could not find news:stock_tickers tag');
        $this->assertEquals('NYSE:OWW, NASDAQ:GTAT', $stockNodes->item(0)->textContent, 'Stock tickers tag did not contain the right value');
    }

    /**
     * Creates an example URL instance for the tests.
     *
     * @return GoogleNewsUrlDecorator
     */
    private function createExampleUrl()
    {
        $url = new GoogleNewsUrlDecorator(
            new UrlConcrete('http://acme.com/'),
            'The Example Times',
            'en',
            new \DateTime(),
            'An example news article'
        );

        return $url;
    }

    /**
     * Generates the urlset XML for a given URL.
     *
     * @param Url $url
     *
     * @return string The rendered XML
     */
    private function generateXml(Url $url)
    {
        $dispatcher = $this->getMockBuilder('Symfony\Component\EventDispatcher\EventDispatcherInterface')->getMock();
        $router = $this->getMockBuilder('Symfony\Component\Routing\RouterInterface')->getMock();
        $router->expects($this->any())
            ->method('generate')
            ->with('PrestaSitemapBundle_section', ['name' => 'default', '_format' => 'xml'], UrlGeneratorInterface::ABSOLUTE_URL)
            ->willReturn('http://site.com/default.xml');

        $section = 'default';
        $generator = new Generator($dispatcher, $router);
        $generator->addUrl($url, 'default');

        return $generator->fetch($section)->toXml();
    }
}
