<?php

/**
 * This file is part of the PrestaSitemapBundle package.
 *
 * (c) PrestaConcept <www.prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle\Test\Sitemap;

use Presta\SitemapBundle\Event\SitemapPopulateEvent;
use Presta\SitemapBundle\Service\Generator;
use Presta\SitemapBundle\Sitemap;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author David Epely <depely@prestaconcept.net>
 */
class GeneratorTest extends WebTestCase
{
    /**
     * @var Generator
     */
    protected $generator;
    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /**
     * @var ContainerInterface
     */
    protected static $container;

    public function setUp()
    {
        self::createClient(['debug' => false]);
        if (self::$container === null) {
            self::$container = self::$kernel->getContainer();
        }
        $this->eventDispatcher = self::$container->get('event_dispatcher');

        $this->generator = new Generator(
            $this->eventDispatcher,
            self::$container->get('router'),
            null,
            null,
            1
        );
    }

    protected function tearDown()
    {
        parent::tearDown();
        self::$container = null;
    }

    public function testGenerate()
    {
        try {
            $this->generator->generate();
            self::assertTrue(true, 'No exception was thrown');
        } catch (\RuntimeException $e) {
            $this->fail('No exception must be thrown');
        }
    }

    public function testFetch()
    {
        $section = $this->generator->generate('void');
        self::assertNull($section);

        $triggered = false;
        $listener = function (SitemapPopulateEvent $event) use (&$triggered) {
            self::assertEquals($event->getSection(), 'foo');
            $triggered = true;
        };
        $this->eventDispatcher->addListener(SitemapPopulateEvent::ON_SITEMAP_POPULATE, $listener);

        $this->generator->fetch('foo');
        self::assertTrue($triggered);
    }

    public function testAddUrl()
    {
        try {
            $this->generator->addUrl(new Sitemap\Url\UrlConcrete('http://acme.com/'), 'default');
            self::assertTrue(true, 'No exception was thrown');
        } catch (\RuntimeException $e) {
            $this->fail('No exception must be thrown');
        }
    }

    public function testGetUrlset()
    {
        $urlset = $this->generator->getUrlset('default');

        self::assertInstanceOf('Presta\\SitemapBundle\\Sitemap\\Urlset', $urlset);
    }

    public function testItemsBySet()
    {
        $url = new Sitemap\Url\UrlConcrete('http://acme.com/');

        $this->generator->addUrl($url, 'default');
        $this->generator->addUrl($url, 'default');

        $fullUrlset  = $this->generator->getUrlset('default_0');
        $emptyUrlset = $this->generator->getUrlset('default_1');

        self::assertEquals(count($fullUrlset), 1);
        self::assertEquals(count($emptyUrlset), 0);
    }

    public function testDefaults()
    {
        $this->generator->setDefaults([
            'priority' => 1,
            'changefreq' => Sitemap\Url\UrlConcrete::CHANGEFREQ_DAILY,
            'lastmod' => 'now',
        ]);

        $url = new Sitemap\Url\UrlConcrete('http://acme.com/');

        self::assertEquals(null, $url->getPriority());
        self::assertEquals(null, $url->getChangefreq());
        self::assertEquals(null, $url->getLastmod());

        $this->generator->addUrl($url, 'default');

        // knowing that the generator changes the url instance, we check its properties here
        self::assertEquals(1, $url->getPriority());
        self::assertEquals(Sitemap\Url\UrlConcrete::CHANGEFREQ_DAILY, $url->getChangefreq());
        self::assertInstanceOf('DateTimeInterface', $url->getLastmod());
    }

    public function testNullableDefaults()
    {
        $this->generator->setDefaults([
            'priority' => null,
            'changefreq' => null,
            'lastmod' => null,
        ]);

        $url = new Sitemap\Url\UrlConcrete('http://acme.com/');

        self::assertEquals(null, $url->getPriority());
        self::assertEquals(null, $url->getChangefreq());
        self::assertEquals(null, $url->getLastmod());

        $this->generator->addUrl($url, 'default');

        self::assertEquals(null, $url->getPriority());
        self::assertEquals(null, $url->getChangefreq());
        self::assertEquals(null, $url->getLastmod());
    }
}
