<?php

namespace Presta\SitemapBundle\Service;

use Doctrine\Common\Cache\Cache;
use Presta\SitemapBundle\Event\SitemapPopulateEvent;
use Presta\SitemapBundle\Sitemap;
use Presta\SitemapBundle\SitemapEvents;
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
	
    protected $cache;
    
    /**
     * @var mixed Sitemapindex or Urlset 
     */
    protected $root;
    
    /**
     * @var array
     */
    protected $urlsets = array();

    public function __construct(ContainerAwareTraceableEventDispatcher $dispatcher, Router $router)
	{
        $this->dispatcher   = $dispatcher;
        $this->router       = $router;
	}
    
    /**
     * Define Cache service
     * 
     * @param Cache $cache 
     */
    public function setCache(Cache $cache = null)
    {
        $this->cache        = $cache;
    }
	
	/**
	 * Generate all datas
	 */
	public function generate()
	{
        //---------------------
        // Populate
        $event = new SitemapPopulateEvent($this);
        $this->dispatcher->dispatch(SitemapEvents::onSitemapPopulate, $event);
        //---------------------
        
        //---------------------
        // cache management
        if ($this->cache) {
            $lifeTime = 3600;
            $this->cache->save('root', serialize($this->root), $lifeTime);
            
            foreach ($this->urlsets as $name => $urlset) {
                $this->cache->save($name, serialize($urlset), $lifeTime);
            }
        }
        //---------------------
	}
    
    
    /**
     * Get eventual cached data or generate whole sitemap
     * 
     * @param string $name
     * @return Sitemapindex or Urlset - can be <null> 
     */
    public function fetch($name)
    {
        if($this->cache && $this->cache->contains($name)) {
            return unserialize($this->cache->fetch($name));
        }
        
        $this->generate();
        
        if( 'root' == $name) {
            return $this->root;
        }
        
        if (array_key_exists($name, $this->urlsets)) {
            return $this->urlsets[$name];
        }
        
        return null;
    }
	
	
    /**
     * add an Url to an Urlset
     * 
     * section is helpfull for partial cache invalidation
     * 
     * //TODO: make $section optional
     * 
     * @param Url\Url $url
     * @param str $section 
     * @throws \RuntimeException 
     */
    public function addUrl(Sitemap\Url\Url $url, $section)
    {
        $urlset = $this->getUrlset($section);
        
        //maximum 50k sitemap in sitemapindex
        $i = 0;
        while ($urlset->isFull() && $i <= Sitemap\Sitemapindex::LIMIT_ITEMS) {
            $urlset = $this->getUrlset($section . '_' . $i);
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
    public function getUrlset($name)
    {
        if (!isset($this->urlsets[$name])) {
            $this->urlsets[$name] = new Sitemap\Urlset($this->router->generate('PrestaSitemapBundle_sitemap', array('name' => $name, '_format' => 'xml'), true));
            
            if (!$this->root) {
                $this->root = new Sitemap\Sitemapindex();
            }
            
            $this->root->addSitemap($this->urlsets[$name]);
        }
        
        return $this->urlsets[$name];
    }
}