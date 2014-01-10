<?php

/**
 * This file is part of the PrestaSitemapBundle
 *
 * (c) PrestaConcept <www.prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle\Tests\Controller;

use Presta\SitemapBundle\Controller;
use Presta\SitemapBundle\Event\SitemapPopulateEvent;
use Presta\SitemapBundle\Service\Generator;
use Presta\SitemapBundle\Sitemap\Url;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SitemapControllerTest extends WebTestCase
{
    public function setUp()
    {
        //boot appKernel
        self::createClient();
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
                    $event->getGenerator()->addUrl(
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
        $response   = $this->controller->indexAction();
        $this->isInstanceOf('Symfony\Component\HttpFoundation\Response', $response);
    }

    public function testValidSectionAction()
    {
        $response = $this->controller->sectionAction('default');
        $this->isInstanceOf('Symfony\Component\HttpFoundation\Response', $response);
    }

    public function testNotFoundSectionAction()
    {
        try {
            $this->controller->sectionAction('void');
            $this->fail('section "void" does\'nt exist');
        } catch (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e) {
            //this is ok
        }
    }
}
