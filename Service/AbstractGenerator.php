<?php

/**
 * This file is part of the PrestaSitemapBundle
 *
 * (c) PrestaConcept <www.prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle\Service;

use Presta\SitemapBundle\Event\SitemapPopulateEvent;
use Presta\SitemapBundle\Sitemap\DumpingUrlset;
use Presta\SitemapBundle\Sitemap\Sitemapindex;
use Presta\SitemapBundle\Sitemap\Url\Url;
use Presta\SitemapBundle\Sitemap\Urlset;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Abstract sitemap generator class
 *
 * @author Konstantin Myakshin <koc-dp@yandex.ru>
 */
abstract class AbstractGenerator
{
    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * @var Sitemapindex
     */
    protected $root;

    /**
     * @var Urlset[]|DumpingUrlset[]
     */
    protected $urlsets = array();

    /**
     * The maximum number of item generated in a sitemap
     * @var int
     */
    protected $itemsBySet;

    /**
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(EventDispatcherInterface $dispatcher, $itemsBySet = null)
    {
        $this->dispatcher = $dispatcher;
        // We add one to LIMIT_ITEMS because it was used as an index, not a
        // quantity
        $this->itemsBySet = ($itemsBySet === null) ? Sitemapindex::LIMIT_ITEMS + 1 : $itemsBySet;
    }

    /**
     * add an Url to an Urlset
     *
     * section is helpfull for partial cache invalidation
     *
     * @param Url    $url
     * @param string $section
     *
     * @throws \RuntimeException
     */
    public function addUrl(Url $url, $section)
    {
        $urlset = $this->getUrlset($section);

        // Compare the number of items in the urlset against the maximum
        // allowed and check the maximum of 50k sitemap in sitemapindex
        $i = 0;
        while ((count($urlset) >= $this->itemsBySet || $urlset->isFull()) && $i <= Sitemapindex::LIMIT_ITEMS) {
            $urlset = $this->getUrlset($section . '_' . $i);
            $i++;
        }

        if (count($urlset) >= $this->itemsBySet || $urlset->isFull()) {
            throw new \RuntimeException('The limit of sitemapindex has been exceeded');
        }

        $urlset->addUrl($url);
    }

    /**
     * get or create urlset
     *
     * @param string $name
     *
     * @return Urlset
     */
    public function getUrlset($name)
    {
        if (!isset($this->urlsets[$name])) {
            $this->urlsets[$name] = $this->newUrlset($name);
        }

        return $this->urlsets[$name];
    }

    /**
     * Factory method for create Urlsets
     *
     * @param string    $name
     * @param \DateTime $lastmod
     *
     * @return Urlset
     */
    abstract protected function newUrlset($name, \DateTime $lastmod = null);

    /**
     * Dispatches SitemapPopulate Event - the listeners should use it to add their URLs to the sitemap
     *
     * @param string|null $section
     */
    protected function populate($section = null)
    {
        $event = new SitemapPopulateEvent($this, $section);
        $this->dispatcher->dispatch(SitemapPopulateEvent::ON_SITEMAP_POPULATE, $event);
    }

    /**
     * @return Sitemapindex
     */
    protected function getRoot()
    {
        if (null === $this->root) {
            $this->root = new Sitemapindex();

            foreach ($this->urlsets as $urlset) {
                $this->root->addSitemap($urlset);
            }
        }

        return $this->root;
    }
}
