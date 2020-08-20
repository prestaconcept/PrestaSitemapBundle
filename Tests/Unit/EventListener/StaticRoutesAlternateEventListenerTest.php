<?php

namespace Presta\SitemapBundle\Tests\Unit\EventListener;

use PHPUnit\Framework\TestCase;
use Presta\SitemapBundle\Event\SitemapAddUrlEvent;
use Presta\SitemapBundle\EventListener\StaticRoutesAlternateEventListener;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface as ContractsEventDispatcherInterface;

class StaticRoutesAlternateEventListenerTest extends TestCase
{
    private const SYMFONY_OPTIONS = ['i18n' => 'symfony', 'default_locale' => 'en', 'locales' => ['en', 'fr']];
    private const JMS_OPTIONS = ['i18n' => 'jms', 'default_locale' => 'en', 'locales' => ['en', 'fr']];

    /**
     * @var UrlGeneratorInterface|ObjectProphecy
     */
    private $router;

    protected function setUp(): void
    {
        $this->router = $this->prophesize(UrlGeneratorInterface::class);
        $this->router->generate('home', [], UrlGeneratorInterface::ABSOLUTE_URL)
            ->willReturn('https://acme.org/');
        $this->router->generate('about', ['_locale' => 'en'], UrlGeneratorInterface::ABSOLUTE_URL)
            ->willReturn('https://acme.org/about');
        $this->router->generate('about', ['_locale' => 'fr'], UrlGeneratorInterface::ABSOLUTE_URL)
            ->willReturn('https://acme.org/a-propos');
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
        $xml = '<url><loc>https://acme.org/about</loc><xhtml:link rel="alternate" hreflang="fr" href="https://acme.org/a-propos" /></url>';
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
        $dispatcher->addSubscriber(new StaticRoutesAlternateEventListener($this->router->reveal(), $listenerOptions));

        $event = new SitemapAddUrlEvent($route, $options);
        if ($dispatcher instanceof ContractsEventDispatcherInterface) {
            $dispatcher->dispatch($event, SitemapAddUrlEvent::NAME);
        } else {
            $dispatcher->dispatch(SitemapAddUrlEvent::NAME, $event);
        }

        return $event;
    }
}
