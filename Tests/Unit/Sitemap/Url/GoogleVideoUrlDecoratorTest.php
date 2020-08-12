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
use Presta\SitemapBundle\Exception\GoogleVideoUrlException;
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

    private function setUpLegacy(): void
    {
        $url = new GoogleVideoUrlDecorator(
            new UrlConcrete('http://acme.com/'),
            'http://acme.com/video/thumbnail.jpg',
            'Acme video',
            'An acme video for testing purposes',
            [
                'content_loc'   => 'http://acme.com/video/content.flv',
                'player_loc'    => 'http://acme.com/video/player.swf?a=b&c=d',
                'duration'      => '600',
                'expiration_date'   => new \DateTime,
                'rating'        => 4.2,
                'view_count'    => 42,
                'publication_date'  => new \DateTime,
                'family_friendly'   => GoogleVideoUrlDecorator::FAMILY_FRIENDLY_YES,
                'category'          => 'Testing w/ spécial chars',
                'restriction_allow' => ['FR', 'BE'],
                'restriction_deny'  => ['GB'],
                'gallery_loc'       => 'http://acme.com/video/gallery/?p=1&sort=desc',
                'gallery_loc_title' => 'Gallery for testing purposes',
                'requires_subscription' => GoogleVideoUrlDecorator::REQUIRES_SUBSCRIPTION_YES,
                'uploader'          => 'depely',
                'uploader_info'     => 'http://acme.com/video/users/1/',
                'platforms'         => [GoogleVideoUrlDecorator::PLATFORM_WEB, GoogleVideoUrlDecorator::PLATFORM_MOBILE],
                'platform_relationship' => GoogleVideoUrlDecorator::PLATFORM_RELATIONSHIP_ALLOW,
                'live'              => GoogleVideoUrlDecorator::LIVE_NO,
            ]
        );

        $url->addTag('acme');
        $url->addTag('testing');
        $url->addPrice(42, 'EUR', GoogleVideoUrlDecorator::PRICE_TYPE_OWN, GoogleVideoUrlDecorator::PRICE_RESOLUTION_HD);
        $url->addPrice(53, 'USD');

        $this->xml = new \DOMDocument();

        $xml = '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"';

        foreach ($url->getCustomNamespaces() as $name => $uri) {
            $xml .= ' xmlns:' . $name . '="' . $uri . '"';
        }

        $xml .= '>' . $url->toXml() . '</urlset>';

        $this->xml->loadXML($xml);
    }

    /**
     * @group legacy
     */
    public function testCountNamespacesLegacy(): void
    {
        $this->setUpLegacy();

        $namespaces = $this->xml->getElementsByTagNameNS('http://www.google.com/schemas/sitemap-video/1.1', '*');
        self::assertEquals(24, $namespaces->length);
    }

    /**
     * @group legacy
     */
    public function testEncodeUrlLegacy(): void
    {
        $this->setUpLegacy();

        $playerLoc = $this->xml->getElementsByTagName('player_loc')->item(0)->nodeValue;
        self::assertEquals($playerLoc, 'http://acme.com/video/player.swf?a=b&c=d');
    }

    /**
     * @group legacy
     */
    public function testRenderCategoryLegacy(): void
    {
        $this->setUpLegacy();

        $category = $this->xml->getElementsByTagName('category')->item(0)->nodeValue;
        self::assertEquals($category, 'Testing w/ spécial chars');
    }

    /**
     * @group legacy
     */
    public function testAccessorsLegacyRequiresConstruct(): void
    {
        $this->expectException(GoogleVideoUrlException::class);
        $this->expectExceptionMessage('thumnail_loc, title and description must be set');
        $url = new GoogleVideoUrlDecorator(new UrlConcrete('http://acme.com'));
        $url->setThumbnailLoc('http://acme.com/video/thumbnail.jpg');
    }

    /**
     * @group legacy
     */
    public function testAccessorsLegacy(): void
    {
        $url = new UrlConcrete('http://acme.com');
        $url = new class($url, 'url', 'title', 'description', ['content_location' => 'url'])
            extends GoogleVideoUrlDecorator {

            public $videos;

            public function addVideo(GoogleVideo $video)
            {
                $this->videos[] = $video;

                return parent::addVideo($video);
            }
        };

        $url->setThumbnailLoc('http://acme.com/video/thumbnail.jpg');
        $url->setTitle('Acme video');
        $url->setDescription('An acme video for testing purposes');
        $url->setContentLoc('An acme video for testing purposes');
        $url->setPlayerLoc('http://acme.com/video/player.swf?a=b&c=d');
        $url->setPlayerLocAllowEmbed(GoogleVideo::PLAYER_LOC_ALLOW_EMBED_NO);
        $url->setPlayerLocAutoplay('ap=1');
        $url->setDuration('600');
        $url->setExpirationDate(new \DateTime('2030-01-01 10:00:00'));
        $url->setRating(4.2);
        $url->setViewCount(42);
        $url->setPublicationDate(new \DateTime('2020-01-01 10:00:00'));
        $url->setFamilyFriendly(GoogleVideo::FAMILY_FRIENDLY_YES);
        $url->setCategory('Awesome Cats');
        $url->setRestrictionAllow(['FR', 'BE']);
        $url->setRestrictionDeny(['GB']);
        $url->setGalleryLoc('http://acme.com/video/gallery/?p=1&sort=desc');
        $url->setGalleryLocTitle('Gallery for testing purposes');
        $url->setRequiresSubscription(GoogleVideo::REQUIRES_SUBSCRIPTION_YES);
        $url->setUploader('depely');
        $url->setUploaderInfo('http://acme.com/video/users/1/');
        $url->setPlatforms([GoogleVideo::PLATFORM_WEB, GoogleVideo::PLATFORM_MOBILE]);
        $url->setPlatformRelationship(GoogleVideo::PLATFORM_RELATIONSHIP_ALLOW);
        $url->setLive(GoogleVideo::LIVE_NO);
        $url->addTag('cat');
        $url->addTag('cute');
        $url->addPrice(10, 'EUR');
        $url->addPrice(12, 'USD');

        $url->toXml(); //default video is added here
        /** @var GoogleVideo $video */
        $video = $url->videos[0];

        self::assertSame('http://acme.com/video/thumbnail.jpg', $url->getThumbnailLoc());
        self::assertSame('http://acme.com/video/thumbnail.jpg', $video->getThumbnailLocation());

        self::assertSame('Acme video', $url->getTitle());
        self::assertSame('Acme video', $video->getTitle());

        self::assertSame('An acme video for testing purposes', $url->getDescription());
        self::assertSame('An acme video for testing purposes', $video->getDescription());

        self::assertSame('An acme video for testing purposes', $url->getContentLoc());
        self::assertSame('An acme video for testing purposes', $video->getContentLocation());

        self::assertSame('http://acme.com/video/player.swf?a=b&c=d', $url->getPlayerLoc());
        self::assertSame('http://acme.com/video/player.swf?a=b&c=d', $video->getPlayerLocation());

        self::assertSame(GoogleVideo::PLAYER_LOC_ALLOW_EMBED_NO, $url->getPlayerLocAllowEmbed());
        self::assertSame(GoogleVideo::PLAYER_LOC_ALLOW_EMBED_NO, $video->getPlayerLocationAllowEmbed());

        self::assertSame('ap=1', $url->getPlayerLocAutoplay());
        self::assertSame('ap=1', $video->getPlayerLocationAutoplay());

        self::assertSame('600', $url->getDuration());
        self::assertSame('600', $video->getDuration());

        self::assertEquals(new \DateTime('2030-01-01 10:00:00'), $url->getExpirationDate());
        self::assertEquals(new \DateTime('2030-01-01 10:00:00'), $video->getExpirationDate());

        self::assertSame(4.2, $url->getRating());
        self::assertSame(4.2, $video->getRating());

        self::assertSame(42, $url->getViewCount());
        self::assertSame(42, $video->getViewCount());

        self::assertEquals(new \DateTime('2020-01-01 10:00:00'), $url->getPublicationDate());
        self::assertEquals(new \DateTime('2020-01-01 10:00:00'), $video->getPublicationDate());

        self::assertSame(GoogleVideo::FAMILY_FRIENDLY_YES, $url->getFamilyFriendly());
        self::assertSame(GoogleVideo::FAMILY_FRIENDLY_YES, $video->getFamilyFriendly());

        self::assertSame('Awesome Cats', $url->getCategory());
        self::assertSame('Awesome Cats', $video->getCategory());

        self::assertSame(['FR', 'BE'], $url->getRestrictionAllow());
        self::assertSame(['FR', 'BE'], $video->getRestrictionAllow());

        self::assertSame(['GB'], $url->getRestrictionDeny());
        self::assertSame(['GB'], $video->getRestrictionDeny());

        self::assertSame('http://acme.com/video/gallery/?p=1&sort=desc', $url->getGalleryLoc());
        self::assertSame('http://acme.com/video/gallery/?p=1&sort=desc', $video->getGalleryLocation());

        self::assertSame('Gallery for testing purposes', $url->getGalleryLocTitle());
        self::assertSame('Gallery for testing purposes', $video->getGalleryLocationTitle());

        self::assertSame(GoogleVideo::REQUIRES_SUBSCRIPTION_YES, $url->getRequiresSubscription());
        self::assertSame(GoogleVideo::REQUIRES_SUBSCRIPTION_YES, $video->getRequiresSubscription());

        self::assertSame('depely', $url->getUploader());
        self::assertSame('depely', $video->getUploader());

        self::assertSame('http://acme.com/video/users/1/', $url->getUploaderInfo());
        self::assertSame('http://acme.com/video/users/1/', $video->getUploaderInfo());

        self::assertSame([GoogleVideo::PLATFORM_WEB, GoogleVideo::PLATFORM_MOBILE], $url->getPlatforms());
        self::assertSame([GoogleVideo::PLATFORM_WEB, GoogleVideo::PLATFORM_MOBILE], $video->getPlatforms());

        self::assertSame(GoogleVideo::PLATFORM_RELATIONSHIP_ALLOW, $url->getPlatformRelationship());
        self::assertSame(GoogleVideo::PLATFORM_RELATIONSHIP_ALLOW, $video->getPlatformRelationship());

        self::assertSame(GoogleVideo::LIVE_NO, $url->getLive());
        self::assertSame(GoogleVideo::LIVE_NO, $video->getLive());

        self::assertSame(['cat', 'cute'], $url->getTags());
        self::assertSame(['cat', 'cute'], $video->getTags());

        $eur = ['amount' => 10, 'currency' => 'EUR', 'type' => null, 'resolution' => null];
        $usd = ['amount' => 12, 'currency' => 'USD', 'type' => null, 'resolution' => null];
        self::assertSame([$eur, $usd], $url->getPrices());
        self::assertSame([$eur, $usd], $video->getPrices());
    }
}
