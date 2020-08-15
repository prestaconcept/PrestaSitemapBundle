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
use Presta\SitemapBundle\Sitemap;

/**
 * @author David Epely <depely@prestaconcept.net>
 */
class GoogleImageTest extends TestCase
{
    /**
     * @dataProvider toXmlProvider
     */
    public function testToXml(
        string $expectedXml,
        string $location,
        string $caption,
        string $geoLocalisation = null,
        string $title = null,
        string $license = null
    ): void {
        $failed = false;
        try {
            $image = new Sitemap\Url\GoogleImage($location, $caption, $geoLocalisation, $title, $license);
        } catch (\RuntimeException $e) {
            $failed = true;
        }

        self::assertFalse($failed, 'An exception must not be thrown');
        self::assertEquals($expectedXml, $image->toXML());
    }

    public function toXmlProvider(): \Generator
    {
        yield [
            '<image:image><image:loc>http://acme.com/logo.jpg</image:loc><image:caption><![CDATA[this is about logo]]></image:caption><image:geo_location><![CDATA[Lyon, France]]></image:geo_location><image:title><![CDATA[The Acme logo]]></image:title><image:license><![CDATA[WTFPL]]></image:license></image:image>',
            'http://acme.com/logo.jpg',
            'this is about logo',
            'Lyon, France',
            'The Acme logo',
            'WTFPL'
        ];
        yield [
            '<image:image><image:loc>http://acme.com/logo.jpg?a=&amp;b=c</image:loc><image:caption><![CDATA[this is about <strong>logo</strong>]]></image:caption></image:image>',
            'http://acme.com/logo.jpg?a=&b=c',
            'this is about <strong>logo</strong>'
        ];
    }

    /**
     * Assert that developers that have extends GoogleVideo
     * and use old named attributes still has functional code with deprecations.
     *
     * @group legacy
     */
    public function testLegacyAccessors(): void
    {
        $image = new class('loc') extends Sitemap\Url\GoogleImage {
            public function getLocLegacy()
            {
                return $this->loc;
            }

            public function setLocLegacy($value)
            {
                $this->loc = $value;
            }

            public function getGeoLocationLegacy()
            {
                return $this->geo_location;
            }

            public function setGeoLocationLegacy($value)
            {
                $this->geo_location = $value;
            }
        };

        $image->setLoc('http://acme.com/logo.jpg');
        self::assertSame('http://acme.com/logo.jpg', $image->getLoc());
        self::assertSame('http://acme.com/logo.jpg', $image->getLocLegacy());
        self::assertSame('http://acme.com/logo.jpg', $image->getLocation());
        $image->setLocLegacy('http://legacy.acme.com/logo.jpg');
        self::assertSame('http://legacy.acme.com/logo.jpg', $image->getLoc());
        self::assertSame('http://legacy.acme.com/logo.jpg', $image->getLocLegacy());
        self::assertSame('http://legacy.acme.com/logo.jpg', $image->getLocation());

        $image->setGeoLocation('Lyon, France');
        self::assertSame('Lyon, France', $image->getGeoLocation());
        self::assertSame('Lyon, France', $image->getGeoLocationLegacy());
        $image->setGeoLocationLegacy('Lugdunum, Gaule');
        self::assertSame('Lugdunum, Gaule', $image->getGeoLocation());
        self::assertSame('Lugdunum, Gaule', $image->getGeoLocationLegacy());
    }
}
