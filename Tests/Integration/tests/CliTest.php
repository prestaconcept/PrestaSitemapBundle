<?php

namespace Presta\SitemapBundle\Tests\Integration\Tests;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class CliTest extends SitemapTestCase
{
    private const PUBLIC_DIR = __DIR__ . '/../public';

    protected function setUp(): void
    {
        foreach (glob(self::PUBLIC_DIR . '/sitemap.*') as $file) {
            if (!@unlink($file)) {
                throw new \RuntimeException('Cannot delete file ' . $file);
            }
        }
    }

    private function index(): string
    {
        return self::PUBLIC_DIR . '/sitemap.xml';
    }

    private function section(string $name, bool $gzip = false): string
    {
        return self::PUBLIC_DIR . '/' . $this->sectionFile($name, $gzip);
    }

    private function sectionFile(string $name, bool $gzip = false): string
    {
        return 'sitemap.' . $name . '.xml' . ($gzip ? '.gz' : '');
    }

    private function fileContent(string $file, bool $gzip = false): string
    {
        if ($gzip === false) {
            return file_get_contents($file);
        }

        $resource = @gzopen($file, 'rb', false);
        if (!$resource) {
            throw new \RuntimeException();
        }

        $data = '';
        while (!gzeof($resource)) {
            $data .= gzread($resource, 1024);
        }
        gzclose($resource);

        return $data;
    }

    public function gzip(): array
    {
        return [
            [false],
            [true],
        ];
    }

    /**
     * @dataProvider gzip
     */
    public function testDumpSitemapUsingCLI(bool $gzip): void
    {
        $index = $this->index();
        self::assertFileNotExists($index, 'Sitemap index file does not exists before dump');

        $static = $this->section('static', $gzip);
        self::assertFileNotExists($static, 'Sitemap "static" section file does not exists before dump');

        $blog = $this->section('blog', $gzip);
        self::assertFileNotExists($blog, 'Sitemap "blog" section file does not exists before dump');

        $archives = $this->section('archives', $gzip);
        $archives0 = $this->section('archives_0', $gzip);
        self::assertFileNotExists($archives, 'Sitemap "archive" section file does not exists before dump');
        self::assertFileNotExists($archives0, 'Sitemap "archive_0" section file does not exists before dump');

        $commandTester = new CommandTester(
            (new Application(self::createKernel()))->find('presta:sitemaps:dump')
        );
        $commandTester->execute(['--gzip' => $gzip]);
        $output = $commandTester->getDisplay();

        self::assertSame(0, $commandTester->getStatusCode(), 'Sitemap dump command succeed');
        foreach (['static', 'blog', 'archives', 'archives_0'] as $section) {
            $file = $this->sectionFile($section, $gzip);
            self::assertStringContainsString($file, $output, '"' . $file . '" was dumped');
        }

        // get sitemap index content via filesystem
        self::assertFileExists($index, 'Sitemap index file exists after dump');
        self::assertIsReadable($index, 'Sitemap index section file is readable');
        self::assertIndex(file_get_contents($index), $gzip);

        // get sitemap "static" section content via filesystem
        self::assertFileExists($static, 'Sitemap "static" section file exists after dump');
        self::assertIsReadable($static, 'Sitemap "static" section file is readable');
        self::assertStaticSection($this->fileContent($static, $gzip));

        // get sitemap "blog" section content via filesystem
        self::assertFileExists($blog, 'Sitemap "blog" section file exists after dump');
        self::assertIsReadable($blog, 'Sitemap "blog" section file is readable');
        self::assertBlogSection($this->fileContent($blog, $gzip));

        // get sitemap "archives" section content via filesystem
        self::assertFileExists($archives, 'Sitemap "archives" section file exists after dump');
        self::assertIsReadable($archives, 'Sitemap "archives" section file is readable');
        self::assertFileExists($archives0, 'Sitemap "archives_0" section file exists after dump');
        self::assertIsReadable($archives0, 'Sitemap "archives_0" section file is readable');
        self::assertArchivesSection($this->fileContent($archives, $gzip));
        self::assertArchivesSection($this->fileContent($archives0, $gzip));
    }

    public function testGzipLinksArePreservedOnPartialDump()
    {
        $command = (new Application(self::createKernel()))->find('presta:sitemaps:dump');

        // dump whole sitemap with gzip
        $commandTester = new CommandTester($command);
        $commandTester->execute(['--gzip' => true]);

        // dump single section with gzip
        $commandTester = new CommandTester($command);
        $commandTester->execute(['--section' => 'static', '--gzip' => true]);

        $index = $this->index();
        self::assertIndex(file_get_contents($index), true);

        $static = $this->section('static', true);
        self::assertStaticSection($this->fileContent($static, true));
        self::assertStaticSection($this->fileContent($static, true));
    }
}
