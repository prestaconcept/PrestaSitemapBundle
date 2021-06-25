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
}
