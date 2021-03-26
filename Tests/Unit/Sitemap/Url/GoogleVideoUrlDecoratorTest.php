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
use Presta\SitemapBundle\Exception\GoogleVideoException;
use Presta\SitemapBundle\Sitemap\Url\GoogleVideo;
use Presta\SitemapBundle\Sitemap\Url\GoogleVideoUrlDecorator;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;

/**
 * @author David Epely <depely@prestaconcept.net>
 */
class GoogleVideoUrlDecoratorTest extends TestCase
{
    /**
     * @var string
     */
    protected $xml;

    protected function setUp(): void
    {
        $url = new GoogleVideoUrlDecorator(new UrlConcrete('http://acme.com/'));
        for ($i=1; $i <=3; $i++) {
            $video = new GoogleVideo(
                "http://acme.com/video/thumbnail$i.jpg",
                "Acme video $i",
                "An acme video for testing purposes ($i)",
                [
                    'content_location'       => "http://acme.com/video/content$i.flv",
                    'player_location'        => 'http://acme.com/video/player.swf?a=b&c=d',
                    'duration'               => '600',
                    'expiration_date'        => new \DateTime(),
                    'rating'                 => 4.2,
                    'view_count'             => 42,
                    'publication_date'       => new \DateTime(),
                    'family_friendly'        => GoogleVideo::FAMILY_FRIENDLY_YES,
                    'category'               => 'Testing w/ spécial chars',
                    'restriction_allow'      => ['FR', 'BE'],
                    'restriction_deny'       => ['GB'],
                    'gallery_location'       => 'http://acme.com/video/gallery/?p=1&sort=desc',
                    'gallery_location_title' => 'Gallery for testing purposes',
                    'requires_subscription'  => GoogleVideo::REQUIRES_SUBSCRIPTION_YES,
                    'uploader'               => 'depely',
                    'uploader_info'          => 'http://acme.com/video/users/1/',
                    'platforms'              => [GoogleVideo::PLATFORM_WEB, GoogleVideo::PLATFORM_MOBILE],
                    'platform_relationship'  => GoogleVideo::PLATFORM_RELATIONSHIP_ALLOW,
                    'live'                   => GoogleVideo::LIVE_NO,
                ]
            );

            $video->addTag('acme');
            $video->addTag('testing');
            $video->addPrice(42, 'EUR', GoogleVideo::PRICE_TYPE_OWN, GoogleVideo::PRICE_RESOLUTION_HD);
            $video->addPrice(53, 'USD');
            $url->addVideo($video);
        }

        $this->xml = new \DOMDocument();

        $xml = '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"';

        foreach ($url->getCustomNamespaces() as $name => $uri) {
            $xml .= ' xmlns:' . $name . '="' . $uri . '"';
        }

        $xml .= '>' . $url->toXml() . '</urlset>';

        $this->xml->loadXML($xml);
    }

    public function testCountNamespaces(): void
    {
        $namespaces = $this->xml->getElementsByTagNameNS('http://www.google.com/schemas/sitemap-video/1.1', '*');
        self::assertEquals(72, $namespaces->length);
    }

    public function testEncodeUrl(): void
    {
        $playerLoc = $this->xml->getElementsByTagName('player_loc')->item(0)->nodeValue;
        self::assertEquals($playerLoc, 'http://acme.com/video/player.swf?a=b&c=d');
    }

    public function testRenderCategory(): void
    {
        $category = $this->xml->getElementsByTagName('category')->item(0)->nodeValue;
        self::assertEquals($category, 'Testing w/ spécial chars');
    }

    public function testItemsLimitExceeded(): void
    {
        $url = new GoogleVideoUrlDecorator(new UrlConcrete('http://acme.com/'));

        $videoTemplate = new GoogleVideo(
            'http://acme.com/video/thumbnail.jpg',
            'Acme video',
            'An acme video for testing purposes',
            ['content_location' => 'http://acme.com/video/content.flv']
        );

        for ($i=0; $i<1000; $i++) {
            $url->addVideo(clone $videoTemplate);
        }

        $this->expectException(GoogleVideoException::class);
        $this->expectExceptionMessage('The video limit has been exceeded');

        $url->addVideo($videoTemplate);
    }

    public function testTagsByVideo(): void
    {
        $xpath = new \DOMXPath($this->xml);
        $xpath->registerNamespace('s', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        $xpath->registerNamespace('v', 'http://www.google.com/schemas/sitemap-video/1.1');

        self::assertEquals('http://acme.com/video/thumbnail1.jpg', $xpath->evaluate('string(/s:urlset/s:url/v:video[1]/v:thumbnail_loc)'));
        self::assertEquals('http://acme.com/video/thumbnail2.jpg', $xpath->evaluate('string(/s:urlset/s:url/v:video[2]/v:thumbnail_loc)'));
        self::assertEquals('http://acme.com/video/thumbnail3.jpg', $xpath->evaluate('string(/s:urlset/s:url/v:video[3]/v:thumbnail_loc)'));

        self::assertEquals('Acme video 1', $xpath->evaluate('string(/s:urlset/s:url/v:video[1]/v:title)'));
        self::assertEquals('Acme video 2', $xpath->evaluate('string(/s:urlset/s:url/v:video[2]/v:title)'));
        self::assertEquals('Acme video 3', $xpath->evaluate('string(/s:urlset/s:url/v:video[3]/v:title)'));

        self::assertEquals('An acme video for testing purposes (1)', $xpath->evaluate('string(/s:urlset/s:url/v:video[1]/v:description)'));
        self::assertEquals('An acme video for testing purposes (2)', $xpath->evaluate('string(/s:urlset/s:url/v:video[2]/v:description)'));
        self::assertEquals('An acme video for testing purposes (3)', $xpath->evaluate('string(/s:urlset/s:url/v:video[3]/v:description)'));

        self::assertEquals('http://acme.com/video/content1.flv', $xpath->evaluate('string(/s:urlset/s:url/v:video[1]/v:content_loc)'));
        self::assertEquals('http://acme.com/video/content2.flv', $xpath->evaluate('string(/s:urlset/s:url/v:video[2]/v:content_loc)'));
        self::assertEquals('http://acme.com/video/content3.flv', $xpath->evaluate('string(/s:urlset/s:url/v:video[3]/v:content_loc)'));
    }
}
