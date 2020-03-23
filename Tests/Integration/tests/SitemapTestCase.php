<?php

namespace Presta\SitemapBundle\Tests\Integration\Tests;

use PHPUnit\Framework\Assert;
use SimpleXMLElement;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class SitemapTestCase extends WebTestCase
{
    protected static function assertIndex(string $xml, bool $gzip = false)
    {
        $index = simplexml_load_string($xml);
        $index->registerXPathNamespace('sm', 'http://www.sitemaps.org/schemas/sitemap/0.9');

        self::assertIndexContainsSectionLink($index, 'static', $gzip);
        self::assertIndexContainsSectionLink($index, 'blog', $gzip);
        self::assertIndexContainsSectionLink($index, 'archives', $gzip);
        self::assertIndexContainsSectionLink($index, 'archives_0', $gzip);
    }

    protected static function assertStaticSection(string $xml)
    {
        $static = simplexml_load_string($xml);
        $static->registerXPathNamespace('sm', 'http://www.sitemaps.org/schemas/sitemap/0.9');

        self::assertSectionContainsCountUrls($static, 'static', 3);
        $annotations = self::assertSectionContainsPath($static, 'static', '/');
        self::assertUrlConcrete($annotations, 'static', 0.5, 'daily');
        $xml = self::assertSectionContainsPath($static, 'static', '/company');
        self::assertUrlConcrete($xml, 'static', 0.7, 'weekly');
        $yaml = self::assertSectionContainsPath($static, 'static', '/contact');
        self::assertUrlConcrete($yaml, 'static', 0.5, 'daily');
    }

    protected static function assertBlogSection(string $xml)
    {
        $blog = simplexml_load_string($xml);
        $blog->registerXPathNamespace('sm', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        $blog->registerXPathNamespace('image', 'http://www.google.com/schemas/sitemap-image/1.1');
        $blog->registerXPathNamespace('video', 'http://www.google.com/schemas/sitemap-video/1.1');

        self::assertSectionContainsCountUrls($blog, 'blog', 5);
        $list = self::assertSectionContainsPath($blog, 'blog', '/blog');
        self::assertUrlConcrete($list, 'blog', 0.5, 'daily');
        $postWithoutMedia = self::assertSectionContainsPath($blog, 'blog', '/blog/post-without-media');
        self::assertUrlConcrete($postWithoutMedia, 'blog', 0.5, 'daily');
        $postWithOneImage = self::assertSectionContainsPath($blog, 'blog', '/blog/post-with-one-image');
        self::assertUrlHasImage($postWithOneImage, 'blog', 'http://lorempixel.com/400/200/technics/1');
        $postWithAVideo = self::assertSectionContainsPath($blog, 'blog', '/blog/post-with-a-video');
        self::assertUrlHasVideo($postWithAVideo, 'blog', 'https://www.youtube.com/watch?v=j6IKRxH8PTg');
        $postWithMultimedia = self::assertSectionContainsPath($blog, 'blog', '/blog/post-with-multimedia');
        self::assertUrlHasImage($postWithMultimedia, 'blog', 'http://lorempixel.com/400/200/technics/2');
        self::assertUrlHasImage($postWithMultimedia, 'blog', 'http://lorempixel.com/400/200/technics/3');
        self::assertUrlHasVideo($postWithMultimedia, 'blog', 'https://www.youtube.com/watch?v=JugaMuswrmk');
    }

    protected static function assertArchivesSection(string $xml)
    {
        $archives = simplexml_load_string($xml);
        $archives->registerXPathNamespace('sm', 'http://www.sitemaps.org/schemas/sitemap/0.9');

        self::assertSectionContainsCountUrls($archives, 'archive', 10);
        Assert::assertCount(
            10,
            $urls = $archives->xpath('//sm:urlset/sm:url[ sm:loc[ contains(text(), "/archive?i=") ] ]'),
            'Sitemap section "archives" contains 10 elements'
        );
        foreach ($urls as $url) {
            self::assertUrlConcrete($url, 'archives', 0.5, 'daily');
        }
    }

    private static function assertIndexContainsSectionLink(
        SimpleXMLElement $xml,
        string $name,
        bool $gzip = false
    ): SimpleXMLElement {
        $loc = sprintf('http://localhost/sitemap.%s.xml', $name);
        if ($gzip) {
            $loc .= '.gz';
        }
        $section = $xml->xpath(
            sprintf('//sm:sitemapindex/sm:sitemap[ sm:loc[ text() = "%s" ] ]', $loc)
        );
        Assert::assertCount(
            1,
            $section,
            'Sitemap index contains a link to "' . $loc . '"'
        );

        return reset($section);
    }

    private static function assertSectionContainsCountUrls(SimpleXMLElement $xml, string $section, int $count)
    {
        Assert::assertCount(
            $count,
            $xml->xpath('//sm:urlset/sm:url'),
            'Sitemap section "' . $section . '" contains ' . $count . ' elements'
        );
    }

    private static function assertSectionContainsPath(
        SimpleXMLElement $xml,
        string $section,
        string $path
    ): SimpleXMLElement {
        $loc = sprintf('http://localhost/%s', ltrim($path, '/'));
        $url = $xml->xpath(
            sprintf('//sm:urlset/sm:url[ sm:loc[ text() = "%s" ] ]', $loc)
        );
        Assert::assertCount(
            1,
            $url,
            'Sitemap section "' . $section . '" contains a link to "' . $loc . '"'
        );

        return reset($url);
    }

    private static function assertUrlConcrete(
        SimpleXMLElement $url,
        string $section,
        float $priority,
        string $changefreq
    ) {
        $loc = (string)$url->loc;
        $locationMessage = 'Sitemap URL "' . $loc . '" of section "' . $section . '"';
        Assert::assertInstanceOf(
            \DateTime::class,
            \DateTime::createFromFormat(DATE_ATOM, $url->lastmod),
            $locationMessage . ' has valid lastmod attribute.'
        );
        Assert::assertSame(
            number_format($priority, 1),
            (string)$url->priority,
            $locationMessage . ' priority attribute is has expected.'
        );
        Assert::assertSame(
            $changefreq,
            (string)$url->changefreq,
            $locationMessage . ' changefreq priority is has expected.'
        );
    }

    private static function assertUrlHasImage(SimpleXMLElement $url, string $section, string $loc)
    {
        $urlLoc = (string)$url->loc;
        Assert::assertCount(
            1,
            $images = $url->xpath(
                sprintf('//image:image[ image:loc[ text() = "%s" ] ]', $loc)
            ),
            'Sitemap URL "' . $urlLoc . '" of section "' . $section . '" has image "' . $loc . '"'
        );
    }

    private static function assertUrlHasVideo(SimpleXMLElement $url, string $section, string $loc)
    {
        $urlLoc = (string)$url->loc;
        Assert::assertCount(
            1,
            $videos = $url->xpath(
                sprintf('//video:video[ video:content_loc[ text() = "%s" ] ]', $loc)
            ),
            'Sitemap URL "' . $urlLoc . '" of section "' . $section . '" has video "' . $loc . '"'
        );
    }
}
