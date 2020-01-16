<?php

namespace Presta\SitemapBundle\Tests\Integration\Tests;

use PHPUnit\Framework\Assert;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class SitemapTestCase extends WebTestCase
{
    private const BASE_URL = 'http://localhost';

    protected static function assertIndex(string $xml)
    {
        self::assertIndexContainsSectionLink($xml, 'static');
        self::assertIndexContainsSectionLink($xml, 'blog');
        //todo more assertions
    }

    protected static function assertStaticSection(string $xml)
    {
        self::assertSectionContainsPath($xml, 'static', '/annotation');
        self::assertSectionContainsPath($xml, 'static', '/yaml');
        self::assertSectionContainsPath($xml, 'static', '/xml');
        //todo more assertions
    }

    protected static function assertBlogSection(string $xml)
    {
        self::assertSectionContainsPath($xml, 'blog', '/blog');
        self::assertSectionContainsPath($xml, 'blog', '/blog/post-without-media');
        self::assertSectionContainsPath($xml, 'blog', '/blog/post-with-one-image');
        self::assertSectionContainsPath($xml, 'blog', '/blog/post-with-a-video');
        self::assertSectionContainsPath($xml, 'blog', '/blog/post-with-multimedia');
        //todo more assertions
    }

    private static function assertIndexContainsSectionLink(string $xml, string $name)
    {
        Assert::assertStringContainsString(
            sprintf('<loc>%s/sitemap.%s.xml</loc>', self::BASE_URL, $name),
            $xml,
            'Sitemap index contains a link to "' . $name . '" section'
        );
    }

    private static function assertSectionContainsPath(string $xml, string $section, string $path)
    {
        Assert::assertStringContainsString(
            sprintf('<loc>%s%s</loc>', self::BASE_URL, $path),
            $xml,
            'Sitemap section "' . $section . '" contains a link to "' . $path . '"'
        );
    }
}
