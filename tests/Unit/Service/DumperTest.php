<?php

/*
 * This file is part of the PrestaSitemapBundle package.
 *
 * (c) PrestaConcept <https://prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle\Tests\Unit\Service;

use Exception;
use PHPUnit\Framework\TestCase;
use Presta\SitemapBundle\Event\SitemapPopulateEvent;
use Presta\SitemapBundle\Service\Dumper;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Routing\Loader\ClosureLoader;
use Symfony\Component\Routing\Router;
use Throwable;

class DumperTest extends TestCase
{
    private const DUMP_DIR = __DIR__ . '/.artifacts';

    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var Dumper
     */
    private $dumper;

    /**
     * @var Router
     */
    private $router;

    public function setUp(): void
    {
        self::removeDir();
        self::createDir();

        $this->eventDispatcher = new EventDispatcher();
        $this->filesystem = new Filesystem();
        $this->router = new Router(new ClosureLoader(), null);
        $this->dumper = new Dumper($this->eventDispatcher, $this->filesystem, 'sitemap', 5, $this->router);

        (new Filesystem())->remove(\glob(sys_get_temp_dir() . '/PrestaSitemaps-*'));
    }

    protected function tearDown(): void
    {
        self::assertTempFilesWereRemoved();
        self::removeDir();
    }

    /**
     * @dataProvider fromScratch
     */
    public function testFromScratch(?string $section, bool $gzip): void
    {
        $hasDefaultSection = \in_array($section, ['default', null], true);
        $hasBlogSection = \in_array($section, ['blog', null], true);
        $hasIndex = $hasDefaultSection || $hasBlogSection;

        if ($hasDefaultSection) {
            $this->eventDispatcher->addListener(SitemapPopulateEvent::ON_SITEMAP_POPULATE, self::defaultListener());
        }
        if ($hasBlogSection) {
            $this->eventDispatcher->addListener(SitemapPopulateEvent::ON_SITEMAP_POPULATE, self::blogListener());
        }

        self::assertEmpty(\glob(self::DUMP_DIR . '/*'), 'Sitemap is empty before test');

        $this->dumper->dump(self::DUMP_DIR, 'https://acme.org', $section, ['gzip' => $gzip]);
        self::assertGeneratedSitemap($gzip, $hasIndex, $hasDefaultSection, $hasBlogSection);
    }

    public function fromScratch(): \Generator
    {
        yield [null, false];
        yield [null, true];
        yield ['default', false];
        yield ['default', true];
        yield ['blog', false];
        yield ['blog', true];
        yield ['unknown', false];
        yield ['unknown', true];
    }

    /**
     * @dataProvider incremental
     */
    public function testIncremental(bool $gzip): void
    {
        $this->eventDispatcher->addListener(SitemapPopulateEvent::ON_SITEMAP_POPULATE, self::defaultListener());
        $this->eventDispatcher->addListener(SitemapPopulateEvent::ON_SITEMAP_POPULATE, self::blogListener());

        self::assertEmpty(\glob(self::DUMP_DIR . '/*'), 'Sitemap is empty before test');

        // first, dump default section only : blog file should not exists
        $this->dumper->dump(self::DUMP_DIR, 'https://acme.org', 'default', ['gzip' => $gzip]);
        self::assertGeneratedSitemap($gzip, true, true, false);

        // then, dump blog section only : both files should exists
        $this->dumper->dump(self::DUMP_DIR, 'https://acme.org', 'blog', ['gzip' => $gzip]);
        self::assertGeneratedSitemap($gzip, true, true, true);
    }

    public function incremental(): \Generator
    {
        yield [false];
        yield [true];
    }

    public function testDirCreated(): void
    {
        $this->eventDispatcher->addListener(SitemapPopulateEvent::ON_SITEMAP_POPULATE, self::defaultListener());

        self::removeDir();

        self::assertDirectoryNotExists(self::DUMP_DIR);
        $this->dumper->dump(self::DUMP_DIR, 'https://acme.org', 'default');
        self::assertDirectoryExists(self::DUMP_DIR);
    }

    /**
     * @dataProvider existingInvalidSitemap
     */
    public function testExistingInvalidSitemap(string $index): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->eventDispatcher->addListener(SitemapPopulateEvent::ON_SITEMAP_POPULATE, self::defaultListener());

        \file_put_contents(self::DUMP_DIR . '/sitemap.xml', $index);
        $this->dumper->dump(self::DUMP_DIR, 'https://acme.org', 'default');
    }

    public function testErrorInListener(): void
    {
        $this->expectException(\Exception::class);
        $this->eventDispatcher->addListener(
            SitemapPopulateEvent::ON_SITEMAP_POPULATE,
            self::errorListener(new Exception('Throw on Unit Test'))
        );

        $this->dumper->dump(self::DUMP_DIR, 'https://acme.org', 'default');
    }

    public function existingInvalidSitemap(): \Generator
    {
        yield [
            <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<sitemapindex xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
              xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/siteindex.xsd"
              xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <sitemap>
        <!-- missing <loc> tag -->
        <lastmod>2020-08-19T20:04:26+02:00</lastmod>
    </sitemap>
</sitemapindex>
XML
            ,
        ];
        yield [
            <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<sitemapindex xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
              xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/siteindex.xsd"
              xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <sitemap>
        <loc>https://acme.org/sitemap.default.xml.gz</loc>
        <!-- missing <lastmod> tag -->
    </sitemap>
</sitemapindex>
XML
            ,
        ];
    }

    private static function createDir(): void
    {
        (new Filesystem())->mkdir(self::DUMP_DIR);
    }

    private static function removeDir(): void
    {
        if (!\is_dir(self::DUMP_DIR)) {
            return;
        }

        (new Filesystem())->remove(self::DUMP_DIR);
    }

    private static function assertGeneratedSitemap(
        bool $gzip,
        bool $hasIndex,
        bool $hasDefaultSection,
        bool $hasBlogSection
    ): void {
        $file = function (?string $section) use ($gzip): string {
            if ($section === null) {
                return self::DUMP_DIR . '/sitemap.xml';
            }

            return self::DUMP_DIR . '/sitemap.' . $section . '.xml' . ($gzip ? '.gz' : '');
        };

        $index = $file(null);
        $default = $file('default');
        $blog = $file('blog');
        $blog0 = $file('blog_0');

        if ($hasIndex) {
            self::assertFileIsReadable($index, 'Sitemap index file is readable');
        }

        if ($hasDefaultSection) {
            self::assertFileIsReadable($default, 'Sitemap "default" section file is readable');
        } else {
            self::assertFileNotExists(
                $default,
                'Sitemap "default" section file does not exists after dumping "blog" section'
            );
        }

        if ($hasBlogSection) {
            self::assertFileIsReadable($blog, 'Sitemap "blog" section file is readable');
            self::assertFileIsReadable($blog0, 'Sitemap "blog_0" section file is readable');
        } else {
            self::assertFileNotExists(
                $blog,
                'Sitemap "blog" section file does not exists after dumping "default" section'
            );
            self::assertFileNotExists(
                $blog0,
                'Sitemap "blog_0 section file does not exists after dumping "default" section'
            );
        }
    }

    private static function assertTempFilesWereRemoved(): void
    {
        self::assertEmpty(\glob(sys_get_temp_dir() . '/PrestaSitemaps-*'));
    }

    private static function defaultListener(): \Closure
    {
        return function (SitemapPopulateEvent $event): void {
            $urls = $event->getUrlContainer();

            if (\in_array($event->getSection(), ['default', null], true)) {
                $urls->addUrl(new UrlConcrete('https://acme.org'), 'default');
                $urls->addUrl(new UrlConcrete('https://acme.org/products'), 'default');
                $urls->addUrl(new UrlConcrete('https://acme.org/contact'), 'default');
                $urls->addUrl(new UrlConcrete('https://acme.org/team'), 'default');
                $urls->addUrl(new UrlConcrete('https://acme.org/jobs'), 'default');
            }
        };
    }

    private static function errorListener(Throwable $exception): \Closure
    {
        return function () use ($exception): void {
            throw $exception;
        };
    }

    private static function blogListener(): \Closure
    {
        return function (SitemapPopulateEvent $event): void {
            $urls = $event->getUrlContainer();

            if (\in_array($event->getSection(), ['blog', null], true)) {
                $urls->addUrl(new UrlConcrete('https://acme.org/blog'), 'blog');
                $urls->addUrl(new UrlConcrete('https://acme.org/blog/categories'), 'blog');
                $urls->addUrl(new UrlConcrete('https://acme.org/blog/category/symfony'), 'blog');
                $urls->addUrl(new UrlConcrete('https://acme.org/blog/category/php'), 'blog');
                $urls->addUrl(new UrlConcrete('https://acme.org/blog/tags'), 'blog');
                $urls->addUrl(new UrlConcrete('https://acme.org/blog/tag/sitemap'), 'blog');
                $urls->addUrl(new UrlConcrete('https://acme.org/blog/tag/seo'), 'blog');
            }
        };
    }
}
