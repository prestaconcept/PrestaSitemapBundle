<?php

/**
 * This file is part of the PrestaSitemapBundle package.
 *
 * (c) PrestaConcept <www.prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle\Service;

use Presta\SitemapBundle\Sitemap\Urlset;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Sitemap Manager service
 *
 * @author David Epely <depely@prestaconcept.net>
 * @author Christophe Dolivet
 * @author Konstantin Myakshin <koc-dp@yandex.ru>
 */
class Generator extends AbstractGenerator implements GeneratorInterface
{
    /**
     * @var UrlGeneratorInterface
     */
    protected $router;

    /**
     * @var AdapterInterface|null
     */
    protected $cache;

    /**
     * @var int|null
     */
    protected $cacheTtl;

    /**
     * @var string|null
     */
    protected $cacheNamespace;

    /**
     * @param EventDispatcherInterface $dispatcher
     * @param UrlGeneratorInterface    $router
     * @param int|null                 $itemsBySet
     * @param int|null                 $cacheTtl
     * @param int|null                 $cacheNamespace
     */
    public function __construct(
        EventDispatcherInterface $dispatcher,
        UrlGeneratorInterface $router,
        $itemsBySet = null,
        $cacheTtl = null,
        $cacheNamespace = null
    ) {
        parent::__construct($dispatcher, $itemsBySet);

        $this->router = $router;
        $this->cacheTtl = $cacheTtl;
        $this->cacheNamespace = $cacheNamespace;
    }

    public function setCachePool(AdapterInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @inheritdoc
     */
    public function generate()
    {
        $this->populate();

        //---------------------
        //---------------------
        // cache management
        if ($this->cache) {
            $this->cacheSaveDeferred('root', $this->getRoot());

            foreach ($this->urlsets as $name => $urlset) {
                $this->cacheSaveDeferred($name, $urlset);
            }

            $this->cache->commit();
        }
        //---------------------
    }

    /**
     * @inheritdoc
     */
    public function fetch($name)
    {
        if ($this->cache) {
            $sitemap = $this->cacheFetch($name);
            if (!is_null($sitemap)) {
                return $sitemap;
            }
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
     * @inheritdoc
     */
    protected function newUrlset($name, \DateTime $lastmod = null)
    {
        return new Urlset(
            $this->router->generate(
                'PrestaSitemapBundle_section',
                ['name' => $name, '_format' => 'xml'],
                UrlGeneratorInterface::ABSOLUTE_URL
            ),
            $lastmod
        );
    }

    /**
     * Deferred save of a name/value in the cache
     *
     * @param $name
     * @param $value
     */
    private function cacheSaveDeferred($name, $value)
    {
        $key = $this->getNamespacedKey($name);
        $cacheItem = $this->cache->getItem($key);
        $cacheItem->set($value);
        $cacheItem->expiresAfter($this->cacheTtl);
        $this->cache->saveDeferred($cacheItem);
    }

    /**
     * Fetch a value from the cache by its name
     *
     * @param $name
     *
     * @return mixed|null
     */
    private function cacheFetch($name)
    {
        $key = $this->getNamespacedKey($name);
        try {
            if ($this->cache->hasItem($key)) {
                $cacheItem = $this->cache->getItem($key);
                if ($cacheItem->isHit()) {
                    return $cacheItem->get();
                }
            }
        } catch (InvalidArgumentException $e) {
            return null;
        }

        return null;
    }

    /**
     * Get namespaced key by its name
     *
     * @param string $name
     *
     * @return string
     */
    private function getNamespacedKey($name)
    {
        return sprintf('%s.%s', $this->cacheNamespace ?: 'presta_sitemap', $name);
    }
}
