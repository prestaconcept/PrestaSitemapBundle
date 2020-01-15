<?php

namespace Presta\SitemapBundle\Tests\Integration\Tests\Sitemap;

use PHPUnit\Framework\Assert;

final class AssertUtil
{
    private const BASE_URL = 'http://localhost';

    public static function assertIndex(string $xml)
    {
        self::assertIndexContainsSectionLink($xml, 'static');
        self::assertIndexContainsSectionLink($xml, 'blog');
        //todo more assertions
    }

    public static function assertStaticSection(string $xml)
    {
        self::assertSectionContainsPath($xml, 'static', '/annotation');
        self::assertSectionContainsPath($xml, 'static', '/yaml');
        self::assertSectionContainsPath($xml, 'static', '/xml');
        //todo more assertions
    }

    public static function assertBlogSection(string $xml)
    {
        self::assertSectionContainsPath($xml, 'blog', '/blog');
        self::assertSectionContainsPath($xml, 'blog', '/blog/foo');
        //todo more assertions
    }

    private static function assertIndexContainsSectionLink(string $xml, string $name)
    {
        $loc = preg_quote(sprintf('%s/sitemap.%s.xml', self::BASE_URL, $name), '#');
        $lastmod = self::approximatedDateAsRegex();

        Assert::assertRegExp(
            sprintf('#<sitemap><loc>%s</loc><lastmod>%s</lastmod></sitemap>#', $loc, $lastmod),
            $xml,
            'Sitemap index contains a link to "' . $name . '" section'
        );
    }

    private static function assertSectionContainsPath(string $xml, string $section, string $path)
    {
        $loc = preg_quote(sprintf('%s%s', self::BASE_URL, $path), '#');
        $lastmod = self::approximatedDateAsRegex();

        Assert::assertRegExp(
            sprintf('#<url><loc>%s</loc><lastmod>%s</lastmod>#', $loc, $lastmod),
            $xml,
            'Sitemap section "' . $section . '" contains a link to "' . $path . '"'
        );
    }

    private static function approximatedDateAsRegex(): string
    {
        return str_replace('s', '[0-9]{2}', preg_quote(date('Y-m-d\TH:i:\sP'), '#'));
    }
}
