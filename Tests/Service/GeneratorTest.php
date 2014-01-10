<?php

/**
 * This file is part of the PrestaSitemapBundle
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

        $this->generator = new Generator($container->get('event_dispatcher'), $container->get('router'));
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
}
