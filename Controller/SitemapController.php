<?php

namespace Presta\SitemapBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;


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
        //vérifier si le sitemap généré est à jour
        $cacheService = $this->get('liip_doctrine_cache.ns.presta_sitemap');
        $cacheService->setNamespace('presta_sitemap');
        
        
        
        if (!$cacheService->contains('root')) {
            //set obj as sitemapindex or urlset
            $obj = $this->get('sitemap.generator')->generate();
            //set in cache
            $cacheService->save('root', serialize($obj), 3600);
        } else {
            $obj = unserialize($cacheService->fetch('root'));
        }
        
        $response = Response::create($obj->toXml());
        
        return $response;
        
//        switch (get_class($obj)){
//            case 'Urlset' :
//                return $this->render('PrestaSitemapBundle:Sitemap:urlset.' . $_format . '.twig', array('urlset' => $obj));
//                
//            case 'Sitemapindex' :
//                return $this->render('PrestaSitemapBundle:Sitemap:sitemapindex.' . $_format . '.twig', array('sitemap' => $obj));
//        }
//            
        
        //générer le sitemap
        
//        $sitemapGenerator = $this->get('sitemap.generator');
//    	$sitemapGenerator->generate();
//    	$file_list = $sitemapGenerator->getGeneratedFileList();
        
        //rendre le sitemap 
//        return $this->render('PrestaSitemapBundle:Sitemap:index.' . $_format . '.twig', array('file_list' => $file_list));
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
    	
    	return $this->render('PrestaSitemapBundle:Sitemap:section.' . $_format . '.twig', array('section' => $section));
    }
}
