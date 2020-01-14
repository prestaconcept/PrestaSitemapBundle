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
use Symfony\Bundle\FrameworkBundle\Controller\ControllerNameParser;
use Symfony\Bundle\FrameworkBundle\Controller\ControllerResolver;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ContainerControllerResolver;

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

    protected function tearDown() : void
    {
        parent::tearDown();
        self::$container = null;
    }

    public function testIndexAction()
    {
        $controller = $this->getController('PrestaSitemapBundle_index', ['_format' => 'xml']);
        self::assertInstanceOf(Controller\SitemapController::class, $controller[0]);
        self::assertSame('indexAction', $controller[1]);

        $response = $this->controller->indexAction();
        self::assertInstanceOf('Symfony\Component\HttpFoundation\Response', $response);
        self::assertEquals('text/xml', $response->headers->get('Content-Type'));
    }

    public function testValidSectionAction()
    {
        $controller = $this->getController('PrestaSitemapBundle_section', ['name' => 'default', '_format' => 'xml']);
        self::assertInstanceOf(Controller\SitemapController::class, $controller[0]);
        self::assertSame('sectionAction', $controller[1]);

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

    private function getController(string $route, array $parameters): array
    {
        $router = self::$container->get('router');
        $url = $router->generate($route, $parameters);
        $attributes = $router->match($url);
        $request = Request::create($url)->duplicate(null, null, $attributes);
        $resolver = new ControllerResolver(self::$container, new ControllerNameParser(self::$kernel));

        return $resolver->getController($request);
    }
}
