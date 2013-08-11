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
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Routing\RouterInterface;
use Presta\SitemapBundle\Sitemap;

/**
 * Sitemap Manager service
 *
 * @author David Epely <depely@prestaconcept.net>
 * @author Christophe Dolivet
 * @author Konstantin Myakshin <koc-dp@yandex.ru>
 */
class Generator extends AbstractGenerator
{
    protected $router;
    protected $cache;
    protected $cacheTtl;

    /**
     * @param EventDispatcherInterface $dispatcher
     * @param RouterInterface $router
     * @param Cache|null $cache
     * @param integer|null $cacheTtl
     */
    public function __construct(EventDispatcherInterface $dispatcher, RouterInterface $router, Cache $cache = null, $cacheTtl = null)
    {
        parent::__construct($dispatcher);
        $this->router = $router;
        $this->cache = $cache;
        $this->cacheTtl = $cacheTtl;
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
            $this->cache->save('root', serialize($this->getRoot()), $this->cacheTtl);

            foreach ($this->urlsets as $name => $urlset) {
                $this->cache->save($name, serialize($urlset), $this->cacheTtl);
            }
        }
        //---------------------
    }

    /**
     * Get eventual cached data or generate whole sitemap
     *
     * @param string $name
     * @return Sitemap\Sitemapindex or Urlset - can be <null>
     */
    public function fetch($name)
    {
        if ($this->cache && $this->cache->contains($name)) {
            return unserialize($this->cache->fetch($name));
        }

        $this->generate();

        if ('root' == $name) {
            return $this->getRoot();
        }

        if (array_key_exists($name, $this->urlsets)) {
            return $this->urlsets[$name];
        }

        return null;
    }

    /**
     * Factory method for create Urlsets
     *
     * @param string $name
     *
     * @return Sitemap\Urlset
     */
    protected function newUrlset($name, \DateTime $lastmod = null)
    {
        return new Sitemap\Urlset(
            $this->router->generate('PrestaSitemapBundle_section', array('name' => $name, '_format' => 'xml'), true),
            $lastmod
        );
    }
}
