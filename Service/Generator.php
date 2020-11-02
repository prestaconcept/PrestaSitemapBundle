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

use Doctrine\Common\Cache\Cache;
use Presta\SitemapBundle\Sitemap\Urlset;
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
     * @var Cache|null
     */
    protected $cache;

    /**
     * @var int|null
     */
    protected $cacheTtl;

    /**
     * @param EventDispatcherInterface $dispatcher
     * @param UrlGeneratorInterface    $router
     * @param Cache|null               $cache
     * @param int|null                 $cacheTtl
     * @param int|null                 $itemsBySet
     */
    public function __construct(
        EventDispatcherInterface $dispatcher,
        UrlGeneratorInterface $router,
        Cache $cache = null,
        $cacheTtl = null,
        $itemsBySet = null
    ) {
        parent::__construct($dispatcher, $itemsBySet);

        $this->router = $router;
        $this->cache = $cache;
        $this->cacheTtl = $cacheTtl;

        if ($cache !== null) {
            @trigger_error(
                'Providing ' . __METHOD__ . ' $cache parameter is deprecated.' .
                ' Cache support has been deprecated since v2.3.2 and will be removed in v3.0.0.',
                E_USER_DEPRECATED
            );
        }
    }

    /**
     * @inheritdoc
     */
    public function generate()
    {
        @trigger_error(
            __METHOD__ . ' is deprecated since v2.3.2 and will be removed in v3.0.0.' .
            ' Use ' . __CLASS__ . '::fetch instead.',
            E_USER_DEPRECATED
        );

        $this->populate();

        //---------------------
        //---------------------
        // cache management
        if ($this->cache) {
            $this->cache->save('root', $this->getRoot(), $this->cacheTtl);

            foreach ($this->urlsets as $name => $urlset) {
                $this->cache->save($name, $urlset, $this->cacheTtl);
            }
        }
        //---------------------
    }

    /**
     * @inheritdoc
     */
    public function fetch($name)
    {
        if ($this->cache && $this->cache->contains($name)) {
            return $this->cache->fetch($name);
        }

        if ('root' === $name) {
            $this->populate();

            return $this->getRoot();
        }

        $this->populate($name);

        if (array_key_exists($name, $this->urlsets)) {
            $urlset = $this->urlsets[$name];
            if ($this->cache) {
                $this->cache->save($name, $urlset, $this->cacheTtl);
            }

            return $urlset;
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    protected function newUrlset($name, \DateTimeInterface $lastmod = null)
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
}
