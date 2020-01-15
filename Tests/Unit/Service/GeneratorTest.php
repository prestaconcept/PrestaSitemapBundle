<?php

/**
 * This file is part of the PrestaSitemapBundle package.
 *
 * (c) PrestaConcept <www.prestaconcept.net>
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

/**
 * @author David Epely <depely@prestaconcept.net>
 */
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

    protected function tearDown(): void
    {
        $this->eventDispatcher =
        $this->router = null;
    }

    public function testGenerate()
    {
        $this->generator()->generate();
        self::assertTrue(true, 'No exception was thrown');
    }

    public function testFetch()
    {
        $generator = $this->generator();

        $section = $generator->fetch('void');
        self::assertNull($section);

        $triggered = false;
        $listener = function (SitemapPopulateEvent $event) use (&$triggered) {
            self::assertEquals($event->getSection(), 'foo');
            $triggered = true;
        };
        $this->eventDispatcher->addListener(SitemapPopulateEvent::ON_SITEMAP_POPULATE, $listener);

        $generator->fetch('foo');
        self::assertTrue($triggered, 'Event listener was triggered');
    }

    public function testAddUrl()
    {
        $url = $this->acmeHome();
        $this->generator()->addUrl($url, 'default');
        self::assertTrue(true, 'No exception was thrown');
    }

    public function testGetUrlset()
    {
        $urlset = $this->generator()->getUrlset('default');

        self::assertInstanceOf(Urlset::class, $urlset);
    }

    public function testItemsBySet()
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

    public function testDefaults()
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

    public function testNullableDefaults()
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
        return new Generator($this->eventDispatcher, $this->router, null, null, self::ITEMS_BY_SET);
    }
}
