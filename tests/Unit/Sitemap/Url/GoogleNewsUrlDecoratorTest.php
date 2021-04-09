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

use DateTime;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Presta\SitemapBundle\Exception\GoogleNewsUrlException;
use Presta\SitemapBundle\Service\Generator;
use Presta\SitemapBundle\Sitemap\Url\GoogleNewsUrlDecorator;
use Presta\SitemapBundle\Sitemap\Url\Url;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

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
    public function testCountNamespaces(): void
    {
        $url = $this->createExampleUrl();
        $dom = new \DOMDocument();
        $dom->loadXML($this->generateXml($url));

        $newsTags = $dom->getElementsByTagNameNS('http://www.google.com/schemas/sitemap-news/0.9', '*');

        self::assertEquals(6, $newsTags->length, 'Could not find news specific tags');
    }

    /**
     * Tests the default W3C format.
     */
    public function testDefaultDateFormat(): void
    {
        $date = new \DateTime('2013-11-05 10:30:55');

        // test default W3C format
        $url = $this->createExampleUrl();
        $url->setPublicationDate($date);
        $dom = new \DOMDocument();
        $dom->loadXML($this->generateXml($url));

        $dateNodes = $dom->getElementsByTagNameNS('http://www.google.com/schemas/sitemap-news/0.9', 'publication_date');
        self::assertEquals(1, $dateNodes->length, 'Could not find news:publication_date tag');
        self::assertEquals($date->format(\DateTime::W3C), $dateNodes->item(0)->textContent, 'Date was not formatted properly');
    }

    /**
     * Test the custom date only format property.
     */
    public function testCustomDateFormat(): void
    {
        $date = new \DateTime('2013-11-05 10:30:55');

        // test date only format
        $url = $this->createExampleUrl();
        $url->setPublicationDate($date);
        $url->setPublicationDateFormat(GoogleNewsUrlDecorator::DATE_FORMAT_DATE);
        $dom = new \DOMDocument();
        $dom->loadXML($this->generateXml($url));

        $dateNodes = $dom->getElementsByTagNameNS('http://www.google.com/schemas/sitemap-news/0.9', 'publication_date');
        self::assertEquals(1, $dateNodes->length, 'Could not find news:publication_date tag');
        self::assertEquals($date->format('Y-m-d'), $dateNodes->item(0)->textContent, 'Date was not formatted properly');
    }

    /**
     * Tests if the news access property is validated properly.
     */
    public function testAccessPropertyValidation(): void
    {
        $url = $this->createExampleUrl();

        $failed = false;
        try {
            $url->setAccess('invalid-access');
        } catch (GoogleNewsUrlException $e) {
            $failed = true;
        }
        self::assertTrue($failed, 'Setting an invalid access string did not fail');

        $failed = false;
        try {
            $url->setAccess(GoogleNewsUrlDecorator::ACCESS_REGISTRATION);
        } catch (GoogleNewsUrlException $e) {
            $failed = true;
        }
        self::assertFalse($failed, 'Setting a valid access failed');

        $dom = new \DOMDocument();
        $dom->loadXML($this->generateXml($url));
        $accessNodes = $dom->getElementsByTagNameNS('http://www.google.com/schemas/sitemap-news/0.9', 'access');
        self::assertEquals(1, $accessNodes->length, 'Could not find news:access tag');
        self::assertEquals('Registration', $accessNodes->item(0)->textContent, 'Acces tag did not contain the right value');
    }

    /**
     * Tests if the news geo location property is validated properly.
     */
    public function testGeoLocationPropertyValidation(): void
    {
        $url = $this->createExampleUrl();

        $failed = false;
        try {
            $url->setGeoLocations('Somewhere in the world');
        } catch (GoogleNewsUrlException $e) {
            $failed = true;
        }
        self::assertTrue($failed, 'Setting an invalid location string did not fail');

        $failed = false;
        try {
            $url->setGeoLocations('Hamburg, Germany');
            $url->setGeoLocations('Detroit, Michigan, USA');
        } catch (GoogleNewsUrlException $e) {
            $failed = true;
        }
        self::assertFalse($failed, 'Setting a valid access failed');

        $dom = new \DOMDocument();
        $dom->loadXML($this->generateXml($url));
        $geoNodes = $dom->getElementsByTagNameNS('http://www.google.com/schemas/sitemap-news/0.9', 'geo_locations');
        self::assertEquals(1, $geoNodes->length, 'Could not find news:geo_locations tag');
        self::assertEquals('Detroit, Michigan, USA', $geoNodes->item(0)->textContent, 'Locations tag did not contain the right value');
    }

    /**
     * Tests the limitation of the stock tickers
     */
    public function testStockTickersLimit(): void
    {
        $url = $this->createExampleUrl();

        $failed = false;
        try {
            $url->setStockTickers(
                [
                    'NYSE:OWW',
                    'NASDAQ:GTAT',
                    'NYSE:AOL',
                    'NASDAQ:ENDP',
                    'CVE:GTA',
                    'NASDAQ:IMGN'
                ]
            );
        } catch (GoogleNewsUrlException $e) {
            $failed = true;
        }
        self::assertTrue($failed, 'Setting to many stock tickers at once did not fail');

        $failed = false;
        try {
            $url->addStockTicker('NYSE:OWW');
            $url->addStockTicker('NASDAQ:GTAT');
            $url->addStockTicker('NYSE:AOL');
            $url->addStockTicker('NASDAQ:ENDP');
            $url->addStockTicker('CVE:GTA');
        } catch (GoogleNewsUrlException $e) {
            $failed = true;
        }
        self::assertFalse($failed, 'Setting a valid amount of stock tickers failed');

        $failed = false;
        try {
            $url->addStockTicker('NASDAQ:IMGN');
        } catch (GoogleNewsUrlException $e) {
            $failed = true;
        }
        self::assertTrue($failed, 'Setting to many stock tickers over the add method did not fail');

        $url->setStockTickers(
            [
                'NYSE:OWW',
                'NASDAQ:GTAT'
            ]
        );
        $dom = new \DOMDocument();
        $dom->loadXML($this->generateXml($url));
        $stockNodes = $dom->getElementsByTagNameNS('http://www.google.com/schemas/sitemap-news/0.9', 'stock_tickers');
        self::assertEquals(1, $stockNodes->length, 'Could not find news:stock_tickers tag');
        self::assertEquals('NYSE:OWW, NASDAQ:GTAT', $stockNodes->item(0)->textContent, 'Stock tickers tag did not contain the right value');
    }

    public function testPublicationLanguageInvalidValue(): void
    {
        $this->expectException(GoogleNewsUrlException::class);
        $this->expectExceptionMessage(
            'Use a 2 oder 3 character long ISO 639 language code. Except for chinese use zh-cn or zh-tw.' .
            'See https://support.google.com/webmasters/answer/74288?hl=en&ref_topic=10078'
        );

        $this->createExampleUrl()->setPublicationLanguage('6 char');
    }

    public function testPublicationDateFormatInvalidValue(): void
    {
        $this->expectException(GoogleNewsUrlException::class);
        $this->expectExceptionMessage(
            'The parameter ' . DATE_COOKIE . ' must be a valid date format.' .
            ' See https://support.google.com/webmasters/answer/74288?hl=en'
        );

        $this->createExampleUrl()->setPublicationDateFormat(DATE_COOKIE);
    }

    /**
     * @dataProvider toXml
     */
    public function testToXml(
        string $expectedXml,
        string $name,
        string $language,
        DateTime $date,
        string $title,
        string $access = null,
        array $genres = [],
        string $geoLocations = null,
        array $keywords = [],
        array $stockTickers = []
    ): void {
        $url = new GoogleNewsUrlDecorator(new UrlConcrete('http://acme.com/'), $name, $language, $date, $title);
        $url->setAccess($access);
        $url->setGenres($genres);
        if ($geoLocations !== null) {
            $url->setGeoLocations($geoLocations);
        }
        $url->setKeywords($keywords);
        $url->setStockTickers($stockTickers);

        self::assertSame($expectedXml, $url->toXml());
    }

    public function toXml(): \Generator
    {
        yield [
            '<url><loc>http://acme.com/</loc><news:news><news:publication><news:name><![CDATA[Symfony Sitemap]]></news:name><news:language>fr</news:language></news:publication><news:publication_date>2020-01-01T10:00:00+00:00</news:publication_date><news:title><![CDATA[Setup sitemap with Symfony]]></news:title></news:news></url>',
            'Symfony Sitemap',
            'fr',
            new DateTime('2020-01-01T10:00:00+00:00'),
            'Setup sitemap with Symfony',
        ];
        yield [
            '<url><loc>http://acme.com/</loc><news:news><news:publication><news:name><![CDATA[Symfony Sitemap]]></news:name><news:language>fr</news:language></news:publication><news:access>Registration</news:access><news:genres>Blog, Tech</news:genres><news:publication_date>2020-01-01T10:00:00+00:00</news:publication_date><news:title><![CDATA[Setup sitemap with Symfony]]></news:title><news:geo_locations>Lyon, France</news:geo_locations><news:keywords>symfony, sitemap</news:keywords><news:stock_tickers>NYSE:OWW, NASDAQ:GTAT</news:stock_tickers></news:news></url>',
            'Symfony Sitemap',
            'fr',
            new DateTime('2020-01-01T10:00:00+00:00'),
            'Setup sitemap with Symfony',
            GoogleNewsUrlDecorator::ACCESS_REGISTRATION,
            ['Blog', 'Tech'],
            'Lyon, France',
            ['symfony', 'sitemap'],
            ['NYSE:OWW', 'NASDAQ:GTAT'],
        ];
    }

    /**
     * Creates an example URL instance for the tests.
     */
    private function createExampleUrl(): GoogleNewsUrlDecorator
    {
        return new GoogleNewsUrlDecorator(
            new UrlConcrete('http://acme.com/'),
            'The Example Times',
            'en',
            new \DateTime(),
            'An example news article'
        );
    }

    /**
     * Generates the urlset XML for a given URL.
     */
    private function generateXml(Url $url): string
    {
        /** @var EventDispatcherInterface|MockObject $eventDispatcher */
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);

        $routes = new RouteCollection();
        $routes->add('PrestaSitemapBundle_section', new Route('/sitemap.{name}.xml.{_format}'));
        $router = new UrlGenerator($routes, new RequestContext());

        $generator = new Generator($eventDispatcher, $router);
        $generator->addUrl($url, 'default');

        return $generator->fetch('default')->toXml();
    }
}
