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

use Presta\SitemapBundle\Event\SitemapPopulateEvent;
use Presta\SitemapBundle\Service\Generator;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;
use Presta\SitemapBundle\Sitemap\Urlset;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Routing\Loader\ClosureLoader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Router;
use Symfony\Component\Routing\RouterInterface;

class GeneratorTest extends WebTestCase
{
    private const ITEMS_BY_SET = 1;

    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;

    /**
     * @var RouterInterface
     */
    private $router;

    public function setUp(): void
    {
        $this->eventDispatcher = new EventDispatcher();
        $this->router = new Router(new ClosureLoader(), function (): RouteCollection {
            $routes = new RouteCollection();
            $routes->add('PrestaSitemapBundle_section', new Route('/sitemap.{name}.{_format}'));

            return $routes;
        });
    }

    public function testFetch(): void
    {
        self::assertNull($this->generator()->fetch('default'));

        $triggered = false;
        $this->eventDispatcher->addListener(SitemapPopulateEvent::class, function (SitemapPopulateEvent $event) use (&$triggered) {
            self::assertEquals($event->getSection(), 'default');
            $event->getUrlContainer()->addUrl(new UrlConcrete('http://acme.com/page-1'), 'default');
            $event->getUrlContainer()->addUrl(new UrlConcrete('http://acme.com/page-2'), 'default');
            $triggered = true;
        });

        $default = $this->generator()->fetch('default');
        self::assertInstanceOf(Urlset::class, $default);
        self::assertStringContainsString('http://acme.com/page-1', $default->toXml());
        self::assertStringNotContainsString('http://acme.com/page-2', $default->toXml());

        $default0 = $this->generator()->fetch('default_0');
        self::assertInstanceOf(Urlset::class, $default0);
        self::assertStringNotContainsString('http://acme.com/page-1', $default0->toXml());
        self::assertStringContainsString('http://acme.com/page-2', $default0->toXml());

        self::assertNull($this->generator()->fetch('default_1'));

        self::assertTrue($triggered, 'Event listener was triggered');
    }

    public function testFetchDoesNotAccumulateAcrossCalls(): void
    {
        $this->eventDispatcher->addListener(SitemapPopulateEvent::class, function (SitemapPopulateEvent $event): void {
            $event->getUrlContainer()->addUrl(new UrlConcrete('http://acme.com/page-1'), 'default');
        });

        // Reuse the same generator instance to emulate a persistent runtime
        // (e.g. FrankenPHP worker mode) where the shared service survives
        // across requests. A high items-by-set limit ensures the URLs would
        // pile up in the same urlset rather than overflow to a new one.
        $generator = new Generator($this->eventDispatcher, $this->router, 100);

        $first = $generator->fetch('default');
        self::assertInstanceOf(Urlset::class, $first);
        self::assertCount(1, $first);

        $second = $generator->fetch('default');
        self::assertInstanceOf(Urlset::class, $second);
        self::assertCount(1, $second, 'URLs must not pile up when fetch() is called again');
    }

    public function testResetClearsAccumulatedState(): void
    {
        $generator = new Generator($this->eventDispatcher, $this->router, 100);

        // Populate the urlset without going through fetch() (which resets on its own).
        $generator->addUrl(new UrlConcrete('http://acme.com/manual'), 'default');
        self::assertCount(1, $generator->getUrlset('default'));

        $generator->reset();

        self::assertCount(0, $generator->getUrlset('default'), 'reset() must clear the accumulated urlsets');
    }

    public function testRouterInjectedIntoEvent(): void
    {
        $eventRouter = null;
        $listener = function(SitemapPopulateEvent $event) use (&$eventRouter) {
            $eventRouter = $event->getUrlGenerator();
        };

        $this->eventDispatcher->addListener(SitemapPopulateEvent::class, $listener);

        $this->generator()->fetch('foo');

        $this->assertSame($this->router, $eventRouter);
    }

    public function testAddUrl(): void
    {
        $url = $this->acmeHome();
        $this->generator()->addUrl($url, 'default');
        self::assertTrue(true, 'No exception was thrown');
    }

    public function testGetUrlset(): void
    {
        $urlset = $this->generator()->getUrlset('default');

        self::assertInstanceOf(Urlset::class, $urlset);
    }

    public function testItemsBySet(): void
    {
        $url = $this->acmeHome();
        $generator = $this->generator();

        $generator->addUrl($url, 'default');
        $generator->addUrl($url, 'default');

        $fullUrlset = $generator->getUrlset('default_0');
        $emptyUrlset = $generator->getUrlset('default_1');

        self::assertEquals(count($fullUrlset), 1);
        self::assertEquals(count($emptyUrlset), 0);
    }

    public function testDefaults(): void
    {
        $url = $this->acmeHome();
        $generator = $this->generator();

        $generator->setDefaults([
            'priority' => 1,
            'changefreq' => UrlConcrete::CHANGEFREQ_DAILY,
            'lastmod' => 'now',
        ]);

        self::assertEquals(null, $url->getPriority());
        self::assertEquals(null, $url->getChangefreq());
        self::assertEquals(null, $url->getLastmod());

        $this->generator()->addUrl($url, 'default');

        // knowing that the generator changes the url instance, we check its properties here
        self::assertEquals(1, $url->getPriority());
        self::assertEquals(UrlConcrete::CHANGEFREQ_DAILY, $url->getChangefreq());
        self::assertInstanceOf('DateTimeInterface', $url->getLastmod());
    }

    public function testNullableDefaults(): void
    {
        $url = $this->acmeHome();
        $generator = $this->generator();

        $generator->setDefaults([
            'priority' => null,
            'changefreq' => null,
            'lastmod' => null,
        ]);

        self::assertEquals(null, $url->getPriority());
        self::assertEquals(null, $url->getChangefreq());
        self::assertEquals(null, $url->getLastmod());

        $generator->addUrl($url, 'default');

        self::assertEquals(null, $url->getPriority());
        self::assertEquals(null, $url->getChangefreq());
        self::assertEquals(null, $url->getLastmod());
    }

    private function acmeHome(): UrlConcrete
    {
        return new UrlConcrete('http://acme.com/');
    }

    private function generator(): Generator
    {
        return new Generator($this->eventDispatcher, $this->router, self::ITEMS_BY_SET);
    }
}
