<?php

/*
 * This file is part of the prestaSitemapPlugin package.
 * (c) David Epely <depely@prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle\Service;

use Presta\SitemapBundle\Event\SitemapPopulateEvent;
use Presta\SitemapBundle\Sitemap;
use Presta\SitemapBundle\Sitemap\Url\Url;
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
     * @var Sitemap\Sitemapindex
     */
    protected $root;

    /**
     * @var Sitemap\Urlset[]|Sitemap\DumpingUrlset[]
     */
    protected $urlsets = array();

    /**
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * add an Url to an Urlset
     *
     * section is helpfull for partial cache invalidation
     * //TODO: make $section optional
     *
     * @param Url    $url
     * @param string $section
     *
     * @throws \RuntimeException
     */
    public function addUrl(Url $url, $section)
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

    /**
     * get or create urlset
     *
     * @param string $name
     *
     * @return Sitemap\Urlset
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
     * @param string $name
     * @param \DateTime $lastmod
     *
     * @return Sitemap\Urlset
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
        $this->dispatcher->dispatch(SitemapPopulateEvent::onSitemapPopulate, $event);
    }

    /**
     * @return Sitemap\Sitemapindex
     */
    protected function getRoot()
    {
        if (null === $this->root) {
            $this->root = new Sitemap\Sitemapindex();

            foreach ($this->urlsets as $urlset) {
                $this->root->addSitemap($urlset);
            }
        }

        return $this->root;
    }
}
