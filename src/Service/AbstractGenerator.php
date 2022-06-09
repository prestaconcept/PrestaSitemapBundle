<?php

/*
 * This file is part of the PrestaSitemapBundle package.
 *
 * (c) PrestaConcept <https://prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle\Service;

use Presta\SitemapBundle\Event\SitemapPopulateEvent;
use Presta\SitemapBundle\Sitemap\Sitemapindex;
use Presta\SitemapBundle\Sitemap\Url\Url;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;
use Presta\SitemapBundle\Sitemap\Url\UrlDecorator;
use Presta\SitemapBundle\Sitemap\Urlset;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Base class for all sitemap generators.
 *
 * @phpstan-type Defaults array{
 *     lastmod: string|null,
 *     changefreq: string|null,
 *     priority: float|string|int|null
 * }
 */
abstract class AbstractGenerator implements UrlContainerInterface
{
    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * @var Sitemapindex|null
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
     * @var UrlGeneratorInterface|null
     */
    protected $urlGenerator;

    /**
     * @var Defaults
     */
    private $defaults;

    /**
     * @param EventDispatcherInterface   $dispatcher
     * @param int|null                   $itemsBySet
     * @param UrlGeneratorInterface|null $urlGenerator
     */
    public function __construct(
        EventDispatcherInterface $dispatcher,
        int $itemsBySet = null,
        UrlGeneratorInterface $urlGenerator = null
    ) {
        if (!$urlGenerator) {
            @trigger_error(
                'Not injecting the $urlGenerator is deprecated and will be required in 4.0.',
                \E_USER_DEPRECATED
            );
        }

        $this->dispatcher = $dispatcher;
        // We add one to LIMIT_ITEMS because it was used as an index, not a quantity
        $this->itemsBySet = ($itemsBySet === null) ? Sitemapindex::LIMIT_ITEMS + 1 : $itemsBySet;
        $this->urlGenerator = $urlGenerator;

        $this->defaults = [
            'priority' => 1,
            'changefreq' => UrlConcrete::CHANGEFREQ_DAILY,
            'lastmod' => 'now',
        ];
    }

    /**
     * @param Defaults $defaults
     */
    public function setDefaults(array $defaults): void
    {
        $this->defaults = $defaults;
    }

    /**
     * @inheritdoc
     */
    public function addUrl(Url $url, string $section): void
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

        $concreteUrl = $this->getUrlConcrete($url);
        if ($concreteUrl instanceof UrlConcrete) {
            if (null === $concreteUrl->getLastmod() && null !== $this->defaults['lastmod']) {
                $concreteUrl->setLastmod(new \DateTimeImmutable($this->defaults['lastmod']));
            }
            if (null === $concreteUrl->getChangefreq()) {
                $concreteUrl->setChangefreq($this->defaults['changefreq']);
            }
            if (null === $concreteUrl->getPriority()) {
                $concreteUrl->setPriority($this->defaults['priority']);
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
     * @param string                  $name
     * @param \DateTimeInterface|null $lastmod
     *
     * @return Urlset
     */
    abstract protected function newUrlset(string $name, \DateTimeInterface $lastmod = null): Urlset;

    /**
     * Dispatches SitemapPopulate Event - the listeners should use it to add their URLs to the sitemap
     *
     * @param string|null $section
     */
    protected function populate(string $section = null): void
    {
        $event = new SitemapPopulateEvent($this, $section, $this->urlGenerator);

        $this->dispatcher->dispatch($event, SitemapPopulateEvent::ON_SITEMAP_POPULATE);
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

    /**
     * @param Url $url
     *
     * @return Url|null
     */
    private function getUrlConcrete(Url $url): ?Url
    {
        if ($url instanceof UrlConcrete) {
            return $url;
        }

        if ($url instanceof UrlDecorator) {
            return $this->getUrlConcrete($url->getUrlDecorated());
        }

        return null;
    }
}
