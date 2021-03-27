<?php

/*
 * This file is part of the PrestaSitemapBundle package.
 *
 * (c) PrestaConcept <https://prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle\Tests\Unit\Messenger;

use PHPUnit\Framework\TestCase;
use Presta\SitemapBundle\Messenger\DumpSitemapMessage;
use Presta\SitemapBundle\Messenger\DumpSitemapMessageHandler;
use Presta\SitemapBundle\Service\DumperInterface;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Loader\ClosureLoader;
use Symfony\Component\Routing\Router;
use Symfony\Component\Routing\RouterInterface;

class DumpSitemapMessageHandlerTest extends TestCase
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

    private $handler;

    protected function setUp(): void
    {
        if (!interface_exists(MessageBusInterface::class)) {
            self::markTestSkipped('Skipping messenger tests, because it is not installed.');

            return;
        }

        $this->router = new Router(new ClosureLoader(), null);
        $this->router->getContext()->fromRequest(Request::create(self::BASE_URL));
        $this->dumper = $this->createMock(DumperInterface::class);
        $this->handler = new DumpSitemapMessageHandler($this->router, $this->dumper, self::TARGET_DIR);
    }

    /**
     * @dataProvider provideCases
     */
    public function testHandle(?string $section, bool $gzip, ?string $baseUrl, ?string $targetDir): void
    {
        $this->dumper->expects(self::once())
            ->method('dump')
            ->with($targetDir ?? self::TARGET_DIR, $baseUrl ?? self::BASE_URL, $section, ['gzip' => $gzip]);

        $this->handler->__invoke((new DumpSitemapMessage($section, $baseUrl, $targetDir, ['gzip' => $gzip])));
    }

    public function testHandleWithInvalidBaseUrl(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid base url. Use fully qualified base url, e.g. http://acme.com/');

        $this->handler->__invoke(new DumpSitemapMessage(null, 'irc://'));
    }

    public function provideCases(): \Generator
    {
        yield 'Entire sitemap' => [null, false, null, null];
        yield 'Entire sitemap with gzip' => [null, true, null, null];
        yield 'Entire sitemap with custom base url' => [null, false, 'https://acme.og/path/to/sitemap/storage/', null];
        yield 'Entire sitemap with custom target dir' => [null, false, null, '/etc/sitemap'];
        yield '"audio" sitemap section' => ['audio', false, null, null];
        yield '"audio" sitemap with gzip' => ['audio', true, null, null];
        yield '"audio" sitemap with custom base url' => [null, false, 'https://acme.og/path/to/sitemap/storage/', null];
        yield '"audio" sitemap with custom target dir' => ['audio', false, null, '/etc/sitemap'];
    }
}
