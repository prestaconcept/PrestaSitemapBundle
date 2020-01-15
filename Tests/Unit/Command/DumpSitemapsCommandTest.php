<?php

/**
 * This file is part of the PrestaSitemapBundle package.
 *
 * (c) PrestaConcept <www.prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle\Tests\Unit\Command;

use PHPUnit\Framework\TestCase;
use Presta\SitemapBundle\Command\DumpSitemapsCommand;
use Presta\SitemapBundle\Service\DumperInterface;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Loader\ClosureLoader;
use Symfony\Component\Routing\Router;
use Symfony\Component\Routing\RouterInterface;

class DumpSitemapsCommandTest extends TestCase
{
    private const BASE_URL = 'https://acme.og/';
    private const TARGET_DIR = '/path/to/sitemap/dir';

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var DumperInterface|ObjectProphecy
     */
    private $dumper;

    protected function setUp(): void
    {
        $this->router = new Router(new ClosureLoader(), null);
        $this->router->getContext()->fromRequest(Request::create(self::BASE_URL));
        $this->dumper = $this->prophesize(DumperInterface::class);
    }

    protected function tearDown(): void
    {
        $this->router =
        $this->dumper = null;
    }

    /**
     * @dataProvider dump
     */
    public function testDumpSitemapSuccessful(?string $section, bool $gzip)
    {
        if ($section === null) {
            $files = ['sitemap.audio.xml', 'sitemap.video.xml'];
        } else {
            $files = ["sitemap.{$section}.xml"];
        }

        $this->dumper->dump(self::TARGET_DIR, self::BASE_URL, $section, ['gzip' => $gzip])
            ->shouldBeCalledTimes(1)
            ->willReturn($files);

        [$status, $display] = $this->executeCommand($section, $gzip);

        self::assertSame(0, $status, 'Command succeed');
        foreach ($files as $file) {
            self::assertStringContainsString($file, $display, '"' . $file . '" was dumped');
        }
    }

    /**
     * @dataProvider dump
     */
    public function testDumpSitemapFailed(?string $section, bool $gzip)
    {
        $this->dumper->dump(self::TARGET_DIR, self::BASE_URL, $section, ['gzip' => $gzip])
            ->shouldBeCalledTimes(1)
            ->willReturn(false);

        [$status,] = $this->executeCommand($section, $gzip);

        self::assertSame(1, $status, 'Command returned an error code');
    }

    public function dump(): \Generator
    {
        yield 'Entire sitemap' => [null, false];
        yield 'Entire sitemap with gzip' => [null, true];
        yield '"audio" sitemap section' => ['audio', false];
        yield '"audio" sitemap with gzip' => ['audio', true];
    }

    private function executeCommand(?string $section, bool $gzip): array
    {
        $options = ['target' => self::TARGET_DIR, '--gzip' => $gzip];
        if ($section !== null) {
            $options['--section'] = $section;
        }

        $command = new DumpSitemapsCommand($this->router, $this->dumper->reveal(), 'public');
        $commandTester = new CommandTester($command);
        $commandTester->execute($options);

        return [$commandTester->getStatusCode(), $commandTester->getDisplay(true)];
    }
}
