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
use Prophecy\Argument;
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

    /**
     * @dataProvider dump
     */
    public function testDumpSitemapSuccessful(?string $section, bool $gzip): void
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
    public function testDumpSitemapFailed(?string $section, bool $gzip): void
    {
        $this->dumper->dump(self::TARGET_DIR, self::BASE_URL, $section, ['gzip' => $gzip])
            ->shouldBeCalledTimes(1)
            ->willReturn(false);

        [$status,] = $this->executeCommand($section, $gzip);

        self::assertSame(1, $status, 'Command returned an error code');
    }

    /**
     * @dataProvider baseUrls
     */
    public function testRouterHost(string $inUrl, string $expectedUrl): void
    {
        $this->router->getContext()->fromRequest(Request::create($inUrl));
        $this->dumper->dump(self::TARGET_DIR, $expectedUrl, null, ['gzip' => false])
            ->shouldBeCalledTimes(1)
            ->willReturn([]);

        [$status,] = $this->executeCommand(null, false);

        self::assertSame(0, $status, 'Command succeed');
    }

    public function testRouterNoHost(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'Router host must be configured to be able to dump the sitemap, please see documentation.'
        );

        $this->router->getContext()->setHost('');
        $this->dumper->dump(Argument::any())
            ->shouldNotBeCalled();

        $this->executeCommand(null, false);
    }

    public function testBaseUrlOption(): void
    {
        $this->dumper->dump(self::TARGET_DIR, 'http://example.dev/', null, ['gzip' => false])
            ->shouldBeCalledTimes(1)
            ->willReturn([]);

        [$status,] = $this->executeCommand(null, false, 'http://example.dev');

        self::assertSame(0, $status, 'Command succeed');
    }

    public function testInvalidBaseUrlOption(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Invalid base url. Use fully qualified base url, e.g. http://acme.com/'
        );

        $this->dumper->dump(Argument::any())
            ->shouldNotBeCalled();

        $this->executeCommand(null, false, 'not an url');
    }

    public function dump(): \Generator
    {
        yield 'Entire sitemap' => [null, false];
        yield 'Entire sitemap with gzip' => [null, true];
        yield '"audio" sitemap section' => ['audio', false];
        yield '"audio" sitemap with gzip' => ['audio', true];
    }

    public function baseUrls(): \Generator
    {
        yield 'Standard http' => ['http://host.org', 'http://host.org/'];
        yield 'Standard http with port' => ['http://host.org:80', 'http://host.org/'];
        yield 'Custom http port' => ['http://host.org:8080', 'http://host.org:8080/'];
        yield 'Standard https' => ['https://host.org', 'https://host.org/'];
        yield 'Standard https with port' => ['https://host.org:443', 'https://host.org/'];
        yield 'Custom https port' => ['https://host.org:8080', 'https://host.org:8080/'];
    }

    private function executeCommand(?string $section, bool $gzip, string $baseUrl = null): array
    {
        $options = ['target' => self::TARGET_DIR, '--gzip' => $gzip];
        if ($section !== null) {
            $options['--section'] = $section;
        }
        if ($baseUrl !== null) {
            $options['--base-url'] = $baseUrl;
        }

        $command = new DumpSitemapsCommand($this->router, $this->dumper->reveal(), 'public');
        $commandTester = new CommandTester($command);
        $commandTester->execute($options);

        return [$commandTester->getStatusCode(), $commandTester->getDisplay(true)];
    }
}
