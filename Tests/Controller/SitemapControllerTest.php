<?php

/**
 * This file is part of the PrestaSitemapBundle package.
 *
 * (c) PrestaConcept <www.prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle\Tests\Controller;

use Presta\SitemapBundle\Event\SitemapPopulateEvent;
use Presta\SitemapBundle\Sitemap\Url;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SitemapControllerTest extends WebTestCase
{
    /**
     * @var Client
     */
    private $client;

    protected function setUp()
    {
        $this->client = static::createClient(['debug' => false]);
        // add url to sitemap
        $container = static::$kernel->getContainer();
        $container->get('event_dispatcher')
            ->addListener(
                SitemapPopulateEvent::ON_SITEMAP_POPULATE,
                function (SitemapPopulateEvent $event) {
                    $event->getUrlContainer()->addUrl(
                        new Url\UrlConcrete(
                            'http://acme.com/static-page.html',
                            new \DateTime(),
                            Url\UrlConcrete::CHANGEFREQ_HOURLY,
                            1
                        ),
                        'default'
                    );
                }
            );
    }

    public function testRoot()
    {
        $crawler = $this->client->request('GET', '/sitemap.xml');

        $this->assertRegExp('{http://localhost/sitemap.default.xml}', $crawler->html());
    }

    public function testDefaultSection()
    {
        $crawler = $this->client->request('GET', '/sitemap.default.xml');

        $this->assertRegExp('{http://acme.com/static-page.html}', $crawler->html());
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function testNotFoundSection()
    {
        $this->client->request('GET', '/sitemap.not-found.xml');
    }
}
