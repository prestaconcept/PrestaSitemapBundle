<?php

/*
 * This file is part of the PrestaSitemapBundle package.
 *
 * (c) PrestaConcept <https://prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle\Tests\Unit\EventListener;

use PHPUnit\Framework\TestCase;
use Presta\SitemapBundle\Event\SitemapAddUrlEvent;
use Presta\SitemapBundle\EventListener\StaticRoutesAlternateEventListener;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class StaticRoutesAlternateEventListenerTest extends TestCase
{
    private const SYMFONY_OPTIONS = ['i18n' => 'symfony', 'default_locale' => 'en', 'locales' => ['en', 'fr']];
    private const JMS_OPTIONS = ['i18n' => 'jms', 'default_locale' => 'en', 'locales' => ['en', 'fr']];

    /**
     * @var UrlGeneratorInterface
     */
    private $router;

    protected function setUp(): void
    {
        $routes = new RouteCollection();
        $routes->add('home', new Route('/'));
        $routes->add(
            'about.en',
            new Route('/about', ['_locale' => 'en', '_canonical_route' => 'about'], ['_locale' => 'en'])
        );
        $routes->add(
            'about.fr',
            new Route('/a-propos', ['_locale' => 'fr', '_canonical_route' => 'about'], ['_locale' => 'fr'])
        );
        $this->router = new UrlGenerator($routes, new RequestContext());
        $this->router->getContext()->fromRequest(Request::create('https://acme.org'));
    }

    /**
     * @dataProvider translated
     */
    public function testTranslatedUrls(
        array $listenerOptions,
        string $route,
        array $options,
        string $xml
    ): void {
        $event = $this->dispatch($listenerOptions, $route, $options);
        self::assertSame($xml, $event->getUrl()->toXml());
    }

    public function translated(): \Generator
    {
        $options = ['lastmod' => null, 'changefreq' => null, 'priority' => null];
        $xml = '<url><loc>https://acme.org/about</loc><xhtml:link rel="alternate" hreflang="en" href="https://acme.org/about" /><xhtml:link rel="alternate" hreflang="fr" href="https://acme.org/a-propos" /></url>';
        yield [
            self::SYMFONY_OPTIONS,
            'about.en',
            $options,
            $xml
        ];
        yield [
            self::JMS_OPTIONS,
            'en__RG__about',
            $options,
            $xml
        ];
    }

    /**
     * @dataProvider skipped
     */
    public function testSkippedUrls(array $listenerOptions, string $route): void
    {
        $event = $this->dispatch($listenerOptions, $route);
        self::assertNull($event->getUrl());
        self::assertFalse($event->shouldBeRegistered());
    }

    public function skipped(): \Generator
    {
        yield [self::SYMFONY_OPTIONS, 'about.fr'];
        yield [self::JMS_OPTIONS, 'fr__RG__about'];
    }

    /**
     * @dataProvider untranslated
     */
    public function testUntranslatedUrls(array $listenerOptions, string $route): void
    {
        $event = $this->dispatch($listenerOptions, $route);
        self::assertNull($event->getUrl());
        self::assertTrue($event->shouldBeRegistered());
    }

    public function untranslated(): \Generator
    {
        yield [self::SYMFONY_OPTIONS, 'home'];
        yield [self::JMS_OPTIONS, 'home'];
        yield [self::SYMFONY_OPTIONS, 'en__RG__about'];
        yield [self::JMS_OPTIONS, 'about.en'];
    }

    private function dispatch(array $listenerOptions, string $route, array $options = []): SitemapAddUrlEvent
    {
        $dispatcher = new EventDispatcher();
        $dispatcher->addSubscriber(new StaticRoutesAlternateEventListener($this->router, $listenerOptions));

        $event = new SitemapAddUrlEvent($route, $options, $this->router);
        $dispatcher->dispatch($event, SitemapAddUrlEvent::class);

        return $event;
    }
}
