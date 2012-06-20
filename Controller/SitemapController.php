<?php

namespace Presta\SitemapBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class SitemapController extends Controller
{
    /**
     * list files in a sitemap.xml
     * 
     * @param $_format
     * @return response
     */
    public function indexAction($_format)
    {
        
    	$sitemapGenerator = $this->get('sitemap.generator');
        
    	$sitemapGenerator->generate();
    	$file_list = $sitemapGenerator->getGeneratedFileList();
    	
        return $this->render('PrestaSitemapBundle:Sitemap:index.' . $_format . '.twig', array('file_list' => $file_list));
    }
    
    
    /**
     * list urls of a section
     * 
     * @param string
     * @return Response
     */
    public function sectionAction($name, $_format)
    {
    	$sitemapGenerator = $this->get('sitemap.generator');
    	 
    	$sitemapGenerator->generate();
    	$section = $sitemapGenerator->getGeneratedFile($name);
    	
    	if (!$section) 
        {
    		throw $this->createNotFoundException('This sitemap file does not exists');
    	}
    	
//    	$o_sitemapGenerator = $this->getNewSitemapGenerator();
    	
    	//return array($o_sitemapGenerator);
    	
    	return $this->render('PrestaSitemapBundle:Sitemap:section.' . $_format . '.twig', array('section' => $section));
    }
}
