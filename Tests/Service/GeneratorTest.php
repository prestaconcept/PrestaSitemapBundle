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
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author David Epely <depely@prestaconcept.net>
 */
class GeneratorTest extends WebTestCase
{
    protected $generator;
    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    public function setUp()
    {
        self::createClient(['debug' => false]);
        $container  = static::$kernel->getContainer();
        $this->eventDispatcher = $container->get('event_dispatcher');

        $this->generator = new Generator($this->eventDispatcher, $container->get('router'), null, null, 1);
    }

    public function testGenerate()
    {
        try {
            $this->generator->generate();
            $this->assertTrue(true, 'No exception was thrown');
        } catch (\RuntimeException $e) {
            $this->fail('No exception must be thrown');
        }
    }

    public function testFetch()
    {
        $section = $this->generator->generate('void');
        $this->assertNull($section);

        $triggered = false;
        $listener = function (SitemapPopulateEvent $event) use (&$triggered) {
            $this->assertEquals($event->getSection(), 'foo');
            $triggered = true;
        };
        $this->eventDispatcher->addListener(SitemapPopulateEvent::ON_SITEMAP_POPULATE, $listener);

        $this->generator->fetch('foo');
        $this->assertTrue($triggered);
    }

    public function testAddUrl()
    {
        try {
            $this->generator->addUrl(new Sitemap\Url\UrlConcrete('http://acme.com/'), 'default');
            $this->assertTrue(true, 'No exception was thrown');
        } catch (\RuntimeException $e) {
            $this->fail('No exception must be thrown');
        }
    }

    public function testGetUrlset()
    {
        $urlset = $this->generator->getUrlset('default');

        $this->assertInstanceOf('Presta\\SitemapBundle\\Sitemap\\Urlset', $urlset);
    }

    public function testItemsBySet()
    {
        $url = new Sitemap\Url\UrlConcrete('http://acme.com/');

        $this->generator->addUrl($url, 'default');
        $this->generator->addUrl($url, 'default');

        $fullUrlset  = $this->generator->getUrlset('default_0');
        $emptyUrlset = $this->generator->getUrlset('default_1');

        $this->assertEquals(count($fullUrlset), 1);
        $this->assertEquals(count($emptyUrlset), 0);
    }

    public function testDefaults()
    {
        $this->generator->setDefaults([
            'priority' => 1,
            'changefreq' => Sitemap\Url\UrlConcrete::CHANGEFREQ_DAILY,
            'lastmod' => 'now',
        ]);

        $url = new Sitemap\Url\UrlConcrete('http://acme.com/');

        $this->assertEquals(null, $url->getPriority());
        $this->assertEquals(null, $url->getChangefreq());
        $this->assertEquals(null, $url->getLastmod());

        $this->generator->addUrl($url, 'default');

        // knowing that the generator changes the url instance, we check its properties here
        $this->assertEquals(1, $url->getPriority());
        $this->assertEquals(Sitemap\Url\UrlConcrete::CHANGEFREQ_DAILY, $url->getChangefreq());
        $this->assertInstanceOf('DateTime', $url->getLastmod());
    }

    public function testNullableDefaults()
    {
        $this->generator->setDefaults([
            'priority' => null,
            'changefreq' => null,
            'lastmod' => null,
        ]);

        $url = new Sitemap\Url\UrlConcrete('http://acme.com/');

        $this->assertEquals(null, $url->getPriority());
        $this->assertEquals(null, $url->getChangefreq());
        $this->assertEquals(null, $url->getLastmod());

        $this->generator->addUrl($url, 'default');

        $this->assertEquals(null, $url->getPriority());
        $this->assertEquals(null, $url->getChangefreq());
        $this->assertEquals(null, $url->getLastmod());
    }
}
