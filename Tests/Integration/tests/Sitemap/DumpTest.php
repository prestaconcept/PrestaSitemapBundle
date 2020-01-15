<?php

namespace Presta\SitemapBundle\Tests\Integration\Tests\Sitemap;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

final class DumpTest extends KernelTestCase
{
    protected function setUp(): void
    {
        DumpUtil::clean();
    }

    public function testDumpSitemapUsingCLI()
    {
        $index = DumpUtil::index();
        self::assertFileNotExists($index, 'Sitemap index file does not exists before dump');

        $static = DumpUtil::section('static');
        self::assertFileNotExists($static, 'Sitemap "static" section file does not exists before dump');

        $blog = DumpUtil::section('blog');
        self::assertFileNotExists($blog, 'Sitemap "blog" section file does not exists before dump');

        $commandTester = new CommandTester(
            (new Application(self::createKernel()))->find('presta:sitemaps:dump')
        );
        $commandTester->execute([]);

        self::assertSame(0, $commandTester->getStatusCode(), 'Sitemap dump command succeed');
        //todo more assertions ?

        // get sitemap index content via filesystem
        self::assertFileExists($index, 'Sitemap index file exists after dump');
        self::assertIsReadable($index, 'Sitemap index section file is readable');
        AssertUtil::assertIndex(file_get_contents($index));

        // get sitemap "static" section content via filesystem
        self::assertFileExists($static, 'Sitemap "static" section file exists after dump');
        self::assertIsReadable($static, 'Sitemap "static" section file is readable');
        AssertUtil::assertStaticSection(file_get_contents($static));

        // get sitemap "blog" section content via filesystem
        self::assertFileExists($blog, 'Sitemap "blog" section file exists after dump');
        self::assertIsReadable($blog, 'Sitemap "blog" section file is readable');
        AssertUtil::assertBlogSection(file_get_contents($blog));
    }
}
