<?php

namespace Presta\SitemapBundle\Sitemap;

use Presta\SitemapBundle\SitemapEvents;
use Presta\SitemapBundle\Event\SitemapPopulateEvent;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpKernel\Debug\ContainerAwareTraceableEventDispatcher;

class Generator
{
	/**
	 * Event dispatcher
	 * @var EventDispatcher
	 */
	protected $dispatcher;
    
    protected $router;
	
	protected $container;
	
	protected $sections = array();
	
	protected $files = array();
	
	protected $builder;
    
    /**
     * @var mixed Sitemapindex or Urlset 
     */
    protected $root;
    
    /**
     * @var array
     */
    protected $urlsets = array();






    public function __construct(Builder $builder, ContainerAwareTraceableEventDispatcher $dispatcher, Router $router)
	{
        $this->builder      = $builder;
        $this->dispatcher   = $dispatcher;
        $this->router       = $router;
	}
	
	
	/**
	 * Generate all datas
	 */
	public function generate()
	{
        $this->populate();
        
        return $this->root;
	}
	
	
	
	protected function populate()
	{
        // TODO : check lifetime
        if(true)
        {
            $event = new SitemapPopulateEvent($this);
            $this->dispatcher->dispatch(SitemapEvents::onSitemapPopulate, $event);
        }
	}
	
    /**
     * add url to default urlset
     * if default is full or is sitemapindex; get the next sitemapindex and add url to it
     * 
     * @param Url\Url $url 
     */
    public function __addUrl(Url\Url $url)
    {
        $root = $this->getRoot();
        
        if (!$root->addUrl($url)) {
            $this->addSitemapUrl($this->generateSitemapName($this->root), $url);
        }
    }
    
    
    /**
     * add an Url to an Urlset
     * 
     * @param str $name
     * @param Url\Url $url
     * @throws \RuntimeException 
     */
    public function addUrlsetUrl($name, Url\Url $url)
    {
        $urlset = $this->getUrlset($name);
        
        //maximum 50k sitemapindex
        $i = 0;
        while ($urlset->isFull() && $i <= Sitemapindex::LIMIT_SITEMAP_NUMBER) {
            $urlset = $this->getUrlset($name . '.' . $i);
            $i++;
        }
        
        if($urlset->isFull())
        {
            throw new \RuntimeException('The limit of sitemapindex has been exceeded');
        }
        
        $urlset->addUrl($url);
    }
    
    
    /**
     * get or create urlset
     * 
     * @param str $name
     * @return Urlset 
     */
    protected function getUrlset($name)
    {
        if (!isset($this->urlsets[$name])) {
            $this->urlsets[$name] = new Urlset($this->router->generate('PrestaSitemapBundle_sitemap', array('name' => $name, '_format' => 'xml'), true));
            
            if (!$this->root) {
                $this->root = new Sitemapindex();
                $this->root->addSitemap($name, $this->urlsets[$name]);
            }
        }
        
        return $this->urlsets[$name];
    }






































    protected function buildOutputFiles()
	{
		
	}
	
	/**
	 * Return a list of generated files of the sitemap
	 * 
	 * @return array {name: generationDate}
	 */
	public function getGeneratedFileList()
	{
		$list = array();
		
		foreach($this->sections as $section)
		{
			$file = $this->builder->buildSectionFiles($section);
            $list[$section->getName()] = $section->getGenerationDate();
		}
		
		return $list;
	}
	
    /**
     * Get generated file by its section's name
     * 
     * @param str $name
     * @return Section - may be null 
     */
    public function getGeneratedFile($name)
    {
        foreach($this->sections as $section){
            if($section->getName() == $name)
            {
                return $section;
            }
        }
        
        return null;
    }
    
    
    /**
     *Get or generate section
     * 
     * @param str $name
     * @param int $lifetime
     * @return Section 
     */
	public function getSection($name, $lifetime)
	{
		if(!array_key_exists($name, $this->sections)) 
		{
			$this->sections[$name] = new Section($name, $lifetime);
		}
		
		return $this->sections[$name];
	}
}