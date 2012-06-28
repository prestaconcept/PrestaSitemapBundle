<?php

namespace Presta\SitemapBundle\Tests\Controller;

use Presta\SitemapBundle\Controller;
use Presta\SitemapBundle\Service\Generator;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SitemapControllerTest extends WebTestCase
{
    public function testIndexAction()
    {
        //boot appKernel
        self::createClient();
        
        $container  = static::$kernel->getContainer();
        $controller = new Controller\SitemapController();
        $controller->setContainer($container);
        
        $response   = $controller->indexAction();
        $this->isInstanceOf('Symfony\Component\HttpFoundation\Response', $response);
    }
    
    public function testSectionAction()
    {
        //boot appKernel
        self::createClient();
        $container = static::$kernel->getContainer();
        
        $controller = new Controller\SitemapController();
        $controller->setContainer($container);
        
        try {
            $controller->sectionAction('void');
            $this->fail('section "void" does\'nt exist');
        } catch (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e) {
            //this is ok
        }
    }
}
