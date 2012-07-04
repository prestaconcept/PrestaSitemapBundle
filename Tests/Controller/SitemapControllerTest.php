<?php

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
            ->addListener(SitemapPopulateEvent::onSitemapPopulate, function(SitemapPopulateEvent $event) {
                $event->getGenerator()->addUrl(
                    new Url\UrlConcrete(
                            'http://acme.com/static-page.html', 
                            new \DateTime(), 
                            Url\UrlConcrete::CHANGEFREQ_HOURLY, 1), 
                        'default');
            });
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
