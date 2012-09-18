<?php

/*
 * This file is part of the prestaSitemapPlugin package.
 * (c) David Epely <depely@prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle\Service;

use Doctrine\Common\Cache\Cache;
use Presta\SitemapBundle\Event\SitemapPopulateEvent;
use Presta\SitemapBundle\Sitemap;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher;
use Presta\SitemapBundle\Sitemap\Sitemapindex;

/**
 * Sitemap Manager service
 * 
 * @author David Epely <depely@prestaconcept.net>
 * @author Christophe Dolivet
 */
class Generator
{
    protected $dispatcher;
    protected $router;
    protected $cache;

    /**
     * @var Sitemapindex
     */
    protected $root;

    /**
     * @var array
     */
    protected $urlsets = array();

    /**
     * @param ContainerAwareEventDispatcher $dispatcher
     * @param Router $router
     * @param Cache $cache 
     */
    public function __construct(ContainerAwareEventDispatcher $dispatcher, Router $router, Cache $cache = null)
    {
        $this->dispatcher = $dispatcher;
        $this->router = $router;
        $this->cache = $cache;
    }

    /**
     * Generate all datas and store in cache if it is possible
     * 
     * @return void
     */
    public function generate()
    {
        $this->populate();

        //---------------------
        //---------------------
        // cache management
        if ($this->cache) {
            $ttl = $this->dispatcher->getContainer()->getParameter('presta_sitemap.timetolive');
            $this->cache->save('root', serialize($this->root), $ttl);

            foreach ($this->urlsets as $name => $urlset) {
                $this->cache->save($name, serialize($urlset), $ttl);
            }
        }
        //---------------------
    }

    protected function populate($section=null)
    {
        $event = new SitemapPopulateEvent($this, $section);
        $this->dispatcher->dispatch(SitemapPopulateEvent::onSitemapPopulate, $event);
    }

    /**
     * Get eventual cached data or generate whole sitemap
     * 
     * @param string $name
     * @return Sitemapindex or Urlset - can be <null> 
     */
    public function fetch($name)
    {
        if ($this->cache && $this->cache->contains($name)) {
            return unserialize($this->cache->fetch($name));
        }

        $this->generate();

        if ('root' == $name) {
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

        if ($urlset->isFull()) {
            //TODO: recursive sitemap index
            throw new \RuntimeException('The limit of sitemapindex has been exceeded');
        }

        $urlset->addUrl($url);
    }

    protected function newUrlset($name)
    {
        return new Sitemap\Urlset($this->router->generate('PrestaSitemapBundle_section', array('name' => $name, '_format' => 'xml'), true));
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
            $this->urlsets[$name] = $this->newUrlset($name);

            if (!$this->root) {
                $this->root = new Sitemap\Sitemapindex();
            }

            $this->root->addSitemap($this->urlsets[$name]);
        }

        return $this->urlsets[$name];
    }
}
