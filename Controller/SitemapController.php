<?php

namespace Presta\SitemapBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;


class SitemapController extends Controller
{
    /**
     * list sitemaps
     * 
     * //TODO: implement basic urlset rendering
     * //TODO: implement sitemapindex composed by sitemapindex
     * 
     * @param $_format
     * @return Response
     */
    public function indexAction()
    {
        $sitemapindex   = $this->get('presta_sitemap.generator')->fetch('root');
        
        if(!$sitemapindex) {
            throw $this->createNotFoundException();
        }
        
        $response       = Response::create($sitemapindex->toXml());
        //TODO: set http cache
        
        return $response;
    }
    
    
    /**
     * list urls of a section
     * 
     * @param string
     * @return Response
     */
    public function sectionAction($name, $_format)
    {
        
        $section   = $this->get('presta_sitemap.generator')->fetch($name);
        
        if(!$section) {
            throw $this->createNotFoundException();
        }
        
        $response  = Response::create($section->toXml());
        //TODO: set http cache
        
        return $response;
    }
}
