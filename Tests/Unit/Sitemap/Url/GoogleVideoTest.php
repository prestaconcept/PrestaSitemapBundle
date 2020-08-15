<?php

namespace Presta\SitemapBundle\Tests\Unit\Sitemap\Url;

use PHPUnit\Framework\TestCase;
use Presta\SitemapBundle\Exception\GoogleVideoException;
use Presta\SitemapBundle\Exception\GoogleVideoTagException;
use Presta\SitemapBundle\Sitemap\Url\GoogleVideo;

final class GoogleVideoTest extends TestCase
{
    public function testConstructorRequiresLocation(): void
    {
        $this->expectException(GoogleVideoException::class);
        $this->expectExceptionMessage('The parameter content_location or content_location is required');
        new GoogleVideo('url', 'title', 'description', []);
    }

    public function testConstructorRequiresPlatformRelationship(): void
    {
        $this->expectException(GoogleVideoException::class);
        $this->expectExceptionMessage('The parameter platform_relationship is required when platform is set');
        new GoogleVideo(
            'url',
            'title',
            'description',
            ['content_location' => 'url', 'platforms' => [GoogleVideo::PLATFORM_WEB]]
        );
    }

    public function testPlayerLocationAllowEmbedValues(): void
    {
        $this->expectException(GoogleVideoException::class);
        $this->expectExceptionMessage('The parameter invalid must be a valid player_location_allow_embed. See http://support.google.com/webmasters/bin/answer.py?hl=en&answer=80472#4');
        new GoogleVideo(
            'url',
            'title',
            'description',
            ['content_location' => 'url', 'player_location_allow_embed' => 'invalid']
        );
    }

    /**
     * @dataProvider durationValues
     */
    public function testDurationValues(int $value): void
    {
        $this->expectException(GoogleVideoException::class);
        $this->expectExceptionMessage("The parameter $value must be a valid duration. See http://support.google.com/webmasters/bin/answer.py?hl=en&answer=80472#4");
        new GoogleVideo(
            'url',
            'title',
            'description',
            ['content_location' => 'url', 'duration' => $value]
        );
    }

    public function durationValues(): \Generator
    {
        yield [-1];
        yield [28801];
    }

    /**
     * @dataProvider ratingValues
     */
    public function testRatingValues(int $value): void
    {
        $this->expectException(GoogleVideoException::class);
        $this->expectExceptionMessage("The parameter $value must be a valid rating. See http://support.google.com/webmasters/bin/answer.py?hl=en&answer=80472#4");
        new GoogleVideo(
            'url',
            'title',
            'description',
            ['content_location' => 'url', 'rating' => $value]
        );
    }

    public function ratingValues(): \Generator
    {
        yield [-1];
        yield [6];
    }

    public function testFamilyFriendlyValues(): void
    {
        $this->expectException(GoogleVideoException::class);
        $this->expectExceptionMessage('The parameter invalid must be a valid family_friendly. See http://support.google.com/webmasters/bin/answer.py?hl=en&answer=80472#4');
        new GoogleVideo(
            'url',
            'title',
            'description',
            ['content_location' => 'url', 'family_friendly' => 'invalid']
        );
    }

    public function testFamilyFriendlyDefault(): void
    {
        $video = new GoogleVideo(
            'url',
            'title',
            'description',
            ['content_location' => 'url', 'family_friendly' => null]
        );
        self::assertSame(GoogleVideo::FAMILY_FRIENDLY_YES, $video->getFamilyFriendly());
    }

    public function testCategoryValues(): void
    {
        $value = str_pad('String with more than 256 chars', 257, '-');
        $this->expectException(GoogleVideoException::class);
        $this->expectExceptionMessage("The parameter $value must be a valid category. See http://support.google.com/webmasters/bin/answer.py?hl=en&answer=80472#4");
        new GoogleVideo(
            'url',
            'title',
            'description',
            ['content_location' => 'url', 'category' => $value]
        );
    }

    public function testRequiresSubscriptionValues(): void
    {
        $this->expectException(GoogleVideoException::class);
        $this->expectExceptionMessage('The parameter invalid must be a valid requires_subscription. See http://support.google.com/webmasters/bin/answer.py?hl=en&answer=80472#4');
        new GoogleVideo(
            'url',
            'title',
            'description',
            ['content_location' => 'url', 'requires_subscription' => 'invalid']
        );
    }

    public function testTagCountLimit(): void
    {
        $this->expectException(GoogleVideoTagException::class);
        $this->expectExceptionMessage('The tags limit of 32 items is exceeded.');
        $video = new GoogleVideo('url', 'title', 'description', ['content_location' => 'url']);
        $count = 1;
        do {
            $video->addTag("Tag #$count");
        } while(++$count <= 33);
    }

    /**
     * @dataProvider toXml
     */
    public function testToXml(
        string $expectedXml,
        string $thumbnail,
        string $title,
        string $description,
        array $parameters
    ): void {
        $video = new GoogleVideo($thumbnail, $title, $description, $parameters);
        self::assertSame($expectedXml, $video->toXml());
    }

    public function toXml(): \Generator
    {
        yield [
            '<video:video><video:thumbnail_loc>http://acme.com/video/thumbnail.jpg</video:thumbnail_loc><video:title><![CDATA[Acme video]]></video:title><video:description><![CDATA[An acme video for testing purposes]]></video:description><video:category><![CDATA[Awesome Cats]]></video:category><video:content_loc>http://acme.com/video/content.flv</video:content_loc><video:duration>600</video:duration><video:rating>4.2</video:rating><video:view_count>42</video:view_count><video:family_friendly>yes</video:family_friendly><video:requires_subscription>yes</video:requires_subscription><video:live>no</video:live><video:expiration_date>2030-01-01T10:00:00+00:00</video:expiration_date><video:publication_date>2020-01-01T10:00:00+00:00</video:publication_date><video:player_loc allow_embed="no" autoplay="ap=1">http://acme.com/video/player.swf?a=b&amp;c=d</video:player_loc><video:restriction relationship="allow">FR BE</video:restriction><video:restriction relationship="deny">GB</video:restriction><video:gallery_loc title="Gallery for testing purposes">http://acme.com/video/gallery/?p=1&amp;sort=desc</video:gallery_loc><video:uploader info="http://acme.com/video/users/1/">depely</video:uploader><video:platform relationship="allow">web mobile</video:platform></video:video>',
            'http://acme.com/video/thumbnail.jpg',
            'Acme video',
            'An acme video for testing purposes',
            [
                'content_location'            => 'http://acme.com/video/content.flv',
                'player_location'             => 'http://acme.com/video/player.swf?a=b&c=d',
                'player_location_allow_embed' => GoogleVideo::PLAYER_LOC_ALLOW_EMBED_NO,
                'player_location_autoplay'    => 'ap=1',
                'duration'                    => '600',
                'expiration_date'             => new \DateTime('2030-01-01T10:00:00+00:00'),
                'rating'                      => 4.2,
                'view_count'                  => 42,
                'publication_date'            => new \DateTime('2020-01-01T10:00:00+00:00'),
                'family_friendly'             => GoogleVideo::FAMILY_FRIENDLY_YES,
                'category'                    => 'Awesome Cats',
                'restriction_allow'           => ['FR', 'BE'],
                'restriction_deny'            => ['GB'],
                'gallery_location'            => 'http://acme.com/video/gallery/?p=1&sort=desc',
                'gallery_location_title'      => 'Gallery for testing purposes',
                'requires_subscription'       => GoogleVideo::REQUIRES_SUBSCRIPTION_YES,
                'uploader'                    => 'depely',
                'uploader_info'               => 'http://acme.com/video/users/1/',
                'platforms'                   => [GoogleVideo::PLATFORM_WEB, GoogleVideo::PLATFORM_MOBILE],
                'platform_relationship'       => GoogleVideo::PLATFORM_RELATIONSHIP_ALLOW,
                'live'                        => GoogleVideo::LIVE_NO,
            ],
        ];
    }

    /**
     * @dataProvider toXmlLegacy
     * @group legacy
     */
    public function testToXmlLegacy(
        string $expectedXml,
        string $thumbnail,
        string $title,
        string $description,
        array $parameters
    ): void {
        $video = new GoogleVideo($thumbnail, $title, $description, $parameters);
        self::assertSame($expectedXml, $video->toXml());
    }

    public function toXmlLegacy(): \Generator
    {
        yield [
            '<video:video><video:thumbnail_loc>http://acme.com/video/thumbnail.jpg</video:thumbnail_loc><video:title><![CDATA[Acme video]]></video:title><video:description><![CDATA[An acme video for testing purposes]]></video:description><video:category><![CDATA[Awesome Cats]]></video:category><video:content_loc>http://acme.com/video/content.flv</video:content_loc><video:duration>600</video:duration><video:rating>4.2</video:rating><video:view_count>42</video:view_count><video:family_friendly>yes</video:family_friendly><video:requires_subscription>yes</video:requires_subscription><video:live>no</video:live><video:expiration_date>2030-01-01T10:00:00+00:00</video:expiration_date><video:publication_date>2020-01-01T10:00:00+00:00</video:publication_date><video:player_loc allow_embed="no" autoplay="ap=1">http://acme.com/video/player.swf?a=b&amp;c=d</video:player_loc><video:restriction relationship="allow">FR BE</video:restriction><video:restriction relationship="deny">GB</video:restriction><video:gallery_loc title="Gallery for testing purposes">http://acme.com/video/gallery/?p=1&amp;sort=desc</video:gallery_loc><video:uploader info="http://acme.com/video/users/1/">depely</video:uploader><video:platform relationship="allow">web mobile</video:platform></video:video>',
            'http://acme.com/video/thumbnail.jpg',
            'Acme video',
            'An acme video for testing purposes',
            [
                'content_loc'            => 'http://acme.com/video/content.flv',
                'player_loc'             => 'http://acme.com/video/player.swf?a=b&c=d',
                'player_loc_allow_embed' => GoogleVideo::PLAYER_LOC_ALLOW_EMBED_NO,
                'player_loc_autoplay'    => 'ap=1',
                'duration'               => '600',
                'expiration_date'        => new \DateTime('2030-01-01T10:00:00+00:00'),
                'rating'                 => 4.2,
                'view_count'             => 42,
                'publication_date'       => new \DateTime('2020-01-01T10:00:00+00:00'),
                'family_friendly'        => GoogleVideo::FAMILY_FRIENDLY_YES,
                'category'               => 'Awesome Cats',
                'restriction_allow'      => ['FR', 'BE'],
                'restriction_deny'       => ['GB'],
                'gallery_loc'            => 'http://acme.com/video/gallery/?p=1&sort=desc',
                'gallery_loc_title'      => 'Gallery for testing purposes',
                'requires_subscription'  => GoogleVideo::REQUIRES_SUBSCRIPTION_YES,
                'uploader'               => 'depely',
                'uploader_info'          => 'http://acme.com/video/users/1/',
                'platforms'              => [GoogleVideo::PLATFORM_WEB, GoogleVideo::PLATFORM_MOBILE],
                'platform_relationship'  => GoogleVideo::PLATFORM_RELATIONSHIP_ALLOW,
                'live'                   => GoogleVideo::LIVE_NO,
            ],
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
        $video = new class('thumbnail', 'title', 'description', ['content_loc' => 'override later'])
            extends GoogleVideo {

            public function getThumbnailLocLegacy()
            {
                return $this->thumbnail_loc;
            }

            public function setThumbnailLocLegacy($value)
            {
                $this->thumbnail_loc = $value;
            }

            public function getContentLocLegacy()
            {
                return $this->content_loc;
            }

            public function setContentLocLegacy($value)
            {
                $this->content_loc = $value;
            }

            public function getPlayerLocLegacy()
            {
                return $this->player_loc;
            }

            public function setPlayerLocLegacy($value)
            {
                $this->player_loc = $value;
            }

            public function getPlayerLocAllowEmbedLegacy()
            {
                return $this->player_loc_allow_embed;
            }

            public function setPlayerLocAllowEmbedLegacy($value)
            {
                $this->player_loc_allow_embed = $value;
            }

            public function getPlayerLocAutoplayLegacy()
            {
                return $this->player_loc_autoplay;
            }

            public function setPlayerLocAutoplayLegacy($value)
            {
                $this->player_loc_autoplay = $value;
            }

            public function getGalleryLocLegacy()
            {
                return $this->gallery_loc;
            }

            public function setGalleryLocLegacy($value)
            {
                $this->gallery_loc = $value;
            }

            public function getGalleryLocTitleLegacy()
            {
                return $this->gallery_loc_title;
            }

            public function setGalleryLocTitleLegacy($value)
            {
                $this->gallery_loc_title = $value;
            }
        };

        $video->setThumbnailLoc('http://acme.com/video/thumbnail.jpg');
        self::assertSame('http://acme.com/video/thumbnail.jpg', $video->getThumbnailLoc());
        self::assertSame('http://acme.com/video/thumbnail.jpg', $video->getThumbnailLocLegacy());
        self::assertSame('http://acme.com/video/thumbnail.jpg', $video->getThumbnailLocation());
        $video->setThumbnailLocLegacy('http://legacy.acme.com/video/thumbnail.jpg');
        self::assertSame('http://legacy.acme.com/video/thumbnail.jpg', $video->getThumbnailLoc());
        self::assertSame('http://legacy.acme.com/video/thumbnail.jpg', $video->getThumbnailLocLegacy());
        self::assertSame('http://legacy.acme.com/video/thumbnail.jpg', $video->getThumbnailLocation());

        $video->setContentLoc('http://acme.com/video/content.flv');
        self::assertSame('http://acme.com/video/content.flv', $video->getContentLoc());
        self::assertSame('http://acme.com/video/content.flv', $video->getContentLocLegacy());
        self::assertSame('http://acme.com/video/content.flv', $video->getContentLocation());
        $video->setContentLocLegacy('http://legacy.acme.com/video/content.flv');
        self::assertSame('http://legacy.acme.com/video/content.flv', $video->getContentLoc());
        self::assertSame('http://legacy.acme.com/video/content.flv', $video->getContentLocLegacy());
        self::assertSame('http://legacy.acme.com/video/content.flv', $video->getContentLocation());

        $video->setPlayerLoc('http://acme.com/video/player.swf?a=b&c=d');
        self::assertSame('http://acme.com/video/player.swf?a=b&c=d', $video->getPlayerLoc());
        self::assertSame('http://acme.com/video/player.swf?a=b&c=d', $video->getPlayerLocLegacy());
        self::assertSame('http://acme.com/video/player.swf?a=b&c=d', $video->getPlayerLocation());
        $video->setPlayerLocLegacy('http://legacy.acme.com/video/player.swf?a=b&c=d');
        self::assertSame('http://legacy.acme.com/video/player.swf?a=b&c=d', $video->getPlayerLoc());
        self::assertSame('http://legacy.acme.com/video/player.swf?a=b&c=d', $video->getPlayerLocLegacy());
        self::assertSame('http://legacy.acme.com/video/player.swf?a=b&c=d', $video->getPlayerLocation());

        $video->setPlayerLocAllowEmbed(GoogleVideo::PLAYER_LOC_ALLOW_EMBED_NO);
        self::assertSame(GoogleVideo::PLAYER_LOC_ALLOW_EMBED_NO, $video->getPlayerLocAllowEmbed());
        self::assertSame(GoogleVideo::PLAYER_LOC_ALLOW_EMBED_NO, $video->getPlayerLocAllowEmbedLegacy());
        self::assertSame(GoogleVideo::PLAYER_LOC_ALLOW_EMBED_NO, $video->getPlayerLocationAllowEmbed());
        $video->setPlayerLocAllowEmbedLegacy(GoogleVideo::PLAYER_LOC_ALLOW_EMBED_YES);
        self::assertSame(GoogleVideo::PLAYER_LOC_ALLOW_EMBED_YES, $video->getPlayerLocAllowEmbed());
        self::assertSame(GoogleVideo::PLAYER_LOC_ALLOW_EMBED_YES, $video->getPlayerLocAllowEmbedLegacy());
        self::assertSame(GoogleVideo::PLAYER_LOC_ALLOW_EMBED_YES, $video->getPlayerLocationAllowEmbed());

        $video->setPlayerLocAutoplay('ap=1');
        self::assertSame('ap=1', $video->getPlayerLocAutoplay());
        self::assertSame('ap=1', $video->getPlayerLocAutoplayLegacy());
        self::assertSame('ap=1', $video->getPlayerLocationAutoplay());
        $video->setPlayerLocAutoplayLegacy('legacy=1');
        self::assertSame('legacy=1', $video->getPlayerLocAutoplay());
        self::assertSame('legacy=1', $video->getPlayerLocAutoplayLegacy());
        self::assertSame('legacy=1', $video->getPlayerLocationAutoplay());

        $video->setGalleryLoc('http://acme.com/video/gallery/?p=1&sort=desc');
        self::assertSame('http://acme.com/video/gallery/?p=1&sort=desc', $video->getGalleryLoc());
        self::assertSame('http://acme.com/video/gallery/?p=1&sort=desc', $video->getGalleryLocLegacy());
        self::assertSame('http://acme.com/video/gallery/?p=1&sort=desc', $video->getGalleryLocation());
        $video->setGalleryLocLegacy('http://legacy.acme.com/video/gallery/?p=1&sort=desc');
        self::assertSame('http://legacy.acme.com/video/gallery/?p=1&sort=desc', $video->getGalleryLoc());
        self::assertSame('http://legacy.acme.com/video/gallery/?p=1&sort=desc', $video->getGalleryLocLegacy());
        self::assertSame('http://legacy.acme.com/video/gallery/?p=1&sort=desc', $video->getGalleryLocation());

        $video->setGalleryLocTitle('Gallery for testing purposes');
        self::assertSame('Gallery for testing purposes', $video->getGalleryLocTitle());
        self::assertSame('Gallery for testing purposes', $video->getGalleryLocTitleLegacy());
        self::assertSame('Gallery for testing purposes', $video->getGalleryLocationTitle());
        $video->setGalleryLocTitleLegacy('Legacy Test Gallery');
        self::assertSame('Legacy Test Gallery', $video->getGalleryLocTitle());
        self::assertSame('Legacy Test Gallery', $video->getGalleryLocTitleLegacy());
        self::assertSame('Legacy Test Gallery', $video->getGalleryLocationTitle());
    }
}
