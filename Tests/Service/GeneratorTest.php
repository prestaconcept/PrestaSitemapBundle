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

use Presta\SitemapBundle\Service\Generator;
use Presta\SitemapBundle\Sitemap;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @author David Epely <depely@prestaconcept.net>
 */
class GeneratorTest extends WebTestCase
{
    protected $generator;

    public function setUp()
    {
        self::createClient();
        $container  = static::$kernel->getContainer();

        $this->generator = new Generator($container->get('event_dispatcher'), $container->get('router'), null, null, 1);
    }

    public function testGenerate()
    {
        try {
            $this->generator->generate();
        } catch (\RuntimeException $e) {
            $this->fail('No exception must be thrown');
        }
    }

    public function testFetch()
    {
        $section = $this->generator->generate('void');
        $this->assertNull($section);
    }

    public function testAddUrl()
    {
        try {
            $this->generator->addUrl(new Sitemap\Url\UrlConcrete('http://acme.com/'), 'default');
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
}
