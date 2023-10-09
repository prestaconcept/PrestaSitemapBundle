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
use Presta\SitemapBundle\Event\SitemapPopulateEvent;
use Presta\SitemapBundle\EventListener\RouteAnnotationEventListener;
use Presta\SitemapBundle\Sitemap\Url\Url;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;
use Presta\SitemapBundle\Sitemap\Url\UrlDecorator;
use Presta\SitemapBundle\Tests\Unit\InMemoryUrlContainer;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Routing\Loader\ClosureLoader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Router;

class RouteAnnotationEventListenerTest extends TestCase
{
    /**
     * @dataProvider routes
     */
    public function testPopulateSitemap(?string $section, array $routes, array $urls): void
    {
        $urlContainer = $this->dispatch($section, $routes);

        // ensure that all expected section were created but not more than expected
        self::assertEquals(\array_keys($urls), $urlContainer->getSections());

        foreach ($urls as $section => $sectionUrls) {
            $urlset = $urlContainer->getUrlset($section);

            // ensure that urlset is filled with expected count of urls
            self::assertCount(\count($sectionUrls), $urlset);

            foreach ($sectionUrls as [$loc, $changefreq, $lastmod, $priority]) {
                $url = $this->findUrl($urlset, $loc);
                self::assertNotNull($url);

                self::assertSame($loc, $url->getLoc());
                self::assertSame($changefreq, $url->getChangefreq());
                self::assertEquals($lastmod, $url->getLastmod());
                self::assertSame($priority, $url->getPriority());
            }
        }
    }

    /**
     * @dataProvider routes
     */
    public function testEventListenerCanPreventUrlFromBeingAddedToSitemap(?string $section, array $routes): void
    {
        $urlContainer = $this->dispatch($section, $routes, function (SitemapAddUrlEvent $event): void {
            $event->preventRegistration();
        });

        self::assertEmpty($urlContainer->getSections());
    }

    public function testEventListenerCanSetUrl(): void
    {
        $urlContainer = $this->dispatch(null, [['home', '/', true]], function (SitemapAddUrlEvent $event): void {
            $event->setUrl(new UrlConcrete('http://localhost/redirect'));
        });

        $urlset = $urlContainer->getUrlset('default');
        self::assertCount(1, $urlset);

        self::assertNull($this->findUrl($urlset, 'http://localhost/'));
        self::assertNotNull($this->findUrl($urlset, 'http://localhost/redirect'));
    }

    public function routes(): \Generator
    {
        // *Route vars : [name, path, sitemap option]
        // *Sitemap vars : [loc, changefreq, lastmod, priority]

        $homepageRoute = ['home', '/', true];
        $homepageSitemap = ['http://localhost/', null, null, null];

        $contactRoute = ['contact', '/contact', ['lastmod' => '2020-01-01 10:00:00', 'priority' => 1]];
        $contactSitemap = ['http://localhost/contact', null, new \DateTimeImmutable('2020-01-01 10:00:00'), 1.0];

        $blogRoute = ['blog', '/blog', ['section' => 'blog', 'changefreq' => 'always']];
        $blogSitemap = ['http://localhost/blog', 'always', null, null];

        yield [
            null,
            [$homepageRoute, $contactRoute, $blogRoute],
            ['default' => [$homepageSitemap, $contactSitemap], 'blog' => [$blogSitemap]]
        ];
        yield [
            'default',
            [$homepageRoute, $contactRoute, $blogRoute],
            ['default' => [$homepageSitemap, $contactSitemap]]
        ];
        yield [
            'blog',
            [$homepageRoute, $contactRoute, $blogRoute],
            ['blog' => [$blogSitemap]]
        ];
    }

    private function dispatch(?string $section, array $routes, ?\Closure $listener = null): InMemoryUrlContainer
    {
        $dispatcher = new EventDispatcher();
        if ($listener !== null) {
            $dispatcher->addListener(SitemapAddUrlEvent::NAME, $listener);
        }

        $router = new Router(
            new ClosureLoader(),
            static function () use ($routes): RouteCollection {
                $collection = new RouteCollection();
                foreach ($routes as [$name, $path, $option]) {
                    $collection->add($name, new Route($path, [], [], ['sitemap' => $option]));
                }

                return $collection;
            },
            ['resource_type' => 'closure']
        );

        $urlContainer = new InMemoryUrlContainer();
        $dispatcher->addSubscriber(new RouteAnnotationEventListener($router, $dispatcher, 'default'));
        $event = new SitemapPopulateEvent($urlContainer, $section, $router);
        $dispatcher->dispatch($event, SitemapPopulateEvent::class);

        return $urlContainer;
    }

    private function findUrl(array $urlset, string $loc): ?UrlConcrete
    {
        foreach ($urlset as $url) {
            $urlConcrete = $this->getUrlConcrete($url);
            if ($urlConcrete === null) {
                continue;
            }

            if ($urlConcrete->getLoc() !== $loc) {
                continue;
            }

            return $urlConcrete;
        }

        return null;
    }

    private function getUrlConcrete(Url $url): ?UrlConcrete
    {
        if ($url instanceof UrlConcrete) {
            return $url;
        }

        if ($url instanceof UrlDecorator) {
            return $this->getUrlConcrete($url->getUrlDecorated());
        }

        return null;
    }
}
