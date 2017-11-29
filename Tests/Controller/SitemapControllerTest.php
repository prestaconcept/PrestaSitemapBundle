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
    private $container;

    public function setUp()
    {
        //boot appKernel
        self::createClient(['debug' => false]);
        $this->container  = static::$kernel->getContainer();

        //set controller to test
        $this->controller = new Controller\SitemapController();
        $this->controller->setContainer($this->container);

        //-------------------
        // add url to sitemap
        $this->container->get('event_dispatcher')
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

    public function testIndexAction()
    {
        $response = $this->controller->indexAction();
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $response);
    }

    public function testValidSectionAction()
    {
        $response = $this->controller->sectionAction('default');
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $response);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function testNotFoundSectionAction()
    {
        $this->controller->sectionAction('void');
    }
}
