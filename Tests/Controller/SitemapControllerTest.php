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

use Presta\SitemapBundle\Controller;
use Presta\SitemapBundle\Event\SitemapPopulateEvent;
use Presta\SitemapBundle\Sitemap\Url;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SitemapControllerTest extends WebTestCase
{
    /**
     * @var Controller\SitemapController
     */
    private $controller;

    /**
     * @var ContainerInterface
     */
    protected static $container;

    public function setUp()
    {
        //boot appKernel
        self::createClient(['debug' => false]);
        if (self::$container === null) {
            self::$container = self::$kernel->getContainer();
        }

        //set controller to test
        $this->controller = new Controller\SitemapController(
            self::$container->get('presta_sitemap.generator'),
            3600
        );

        //-------------------
        // add url to sitemap
        self::$container->get('event_dispatcher')
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
        //-------------------
    }

    protected function tearDown()
    {
        parent::tearDown();
        self::$container = null;
    }

    public function testIndexAction()
    {
        $response = $this->controller->indexAction();
        self::assertInstanceOf('Symfony\Component\HttpFoundation\Response', $response);
        self::assertEquals('text/xml', $response->headers->get('Content-Type'));
    }

    public function testValidSectionAction()
    {
        $response = $this->controller->sectionAction('default');
        self::assertInstanceOf('Symfony\Component\HttpFoundation\Response', $response);
        self::assertEquals('text/xml', $response->headers->get('Content-Type'));
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function testNotFoundSectionAction()
    {
        $this->controller->sectionAction('void');
    }
}
