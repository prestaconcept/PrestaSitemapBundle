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

use Presta\SitemapBundle\Event\SitemapPopulateEvent;
use Presta\SitemapBundle\Sitemap\Sitemapindex;
use Presta\SitemapBundle\Sitemap\Url\Url;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;
use Presta\SitemapBundle\Sitemap\Urlset;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Abstract sitemap generator class
 *
 * @author Konstantin Myakshin <koc-dp@yandex.ru>
 */
abstract class AbstractGenerator implements UrlContainerInterface
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
     * @var Urlset[]
     */
    protected $urlsets = [];

    /**
     * The maximum number of item generated in a sitemap
     * @var int
     */
    protected $itemsBySet;

    /**
     * @var array
     */
    private $defaults;

    /**
     * @param EventDispatcherInterface $dispatcher
     * @param int|null                 $itemsBySet
     */
    public function __construct(EventDispatcherInterface $dispatcher, int $itemsBySet = null)
    {
        $this->dispatcher = $dispatcher;
        // We add one to LIMIT_ITEMS because it was used as an index, not a quantity
        $this->itemsBySet = ($itemsBySet === null) ? Sitemapindex::LIMIT_ITEMS + 1 : $itemsBySet;

        $this->defaults = [
            'priority' => 1,
            'changefreq' => UrlConcrete::CHANGEFREQ_DAILY,
            'lastmod' => 'now',
        ];
    }

    /**
     * @param array $defaults
     */
    public function setDefaults(array $defaults)
    {
        $this->defaults = $defaults;
    }

    /**
     * @inheritdoc
     */
    public function addUrl(Url $url, string $section)
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

        if ($url instanceof UrlConcrete) {
            if (null === $url->getLastmod()) {
                $url->setLastmod(new \DateTime($this->defaults['lastmod']));
            }
            if (null === $url->getChangefreq()) {
                $url->setChangefreq($this->defaults['changefreq']);
            }
            if (null === $url->getPriority()) {
                $url->setPriority($this->defaults['priority']);
            }
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
    public function getUrlset(string $name): Urlset
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
    abstract protected function newUrlset(string $name, \DateTime $lastmod = null): Urlset;

    /**
     * Dispatches SitemapPopulate Event - the listeners should use it to add their URLs to the sitemap
     *
     * @param string|null $section
     */
    protected function populate(string $section = null)
    {
        $event = new SitemapPopulateEvent($this, $section);
        $this->dispatcher->dispatch(SitemapPopulateEvent::ON_SITEMAP_POPULATE, $event);
    }

    /**
     * @return Sitemapindex
     */
    protected function getRoot(): Sitemapindex
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
