<?php

/*
 * This file is part of the PrestaSitemapBundle package.
 *
 * (c) PrestaConcept <https://prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle\Event;

use LogicException;
use Presta\SitemapBundle\Routing\RouteOptionParser;
use Presta\SitemapBundle\Sitemap\Url\Url;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Event called whenever a static url is about to be added to sitemap.
 *
 * Subscribe to this event if :
 *  - you want to decorate Url
 *  - you want to prevent Url from being added
 *
 * @phpstan-import-type RouteOptions from RouteOptionParser
 */
class SitemapAddUrlEvent extends Event
{
    /**
     * @Event("Presta\SitemapBundle\Event\SitemapAddUrlEvent")
     * @deprecated since presta/sitemap-bundle 3.3, use `SitemapAddUrlEvent::class` instead.
     */
    public const NAME = 'presta_sitemap.add_url';

    /**
     * @var bool
     */
    private $shouldBeRegistered = true;

    /**
     * @var Url|null
     */
    private $url;

    /**
     * @var string
     */
    private $route;

    /**
     * @var RouteOptions
     */
    private $options;

    /**
     * @var UrlGeneratorInterface|null
     */
    protected $urlGenerator;

    /**
     * @param string                     $route
     * @param RouteOptions               $options
     * @param UrlGeneratorInterface|null $urlGenerator
     */
    public function __construct(string $route, array $options, UrlGeneratorInterface $urlGenerator = null)
    {
        $this->route = $route;
        $this->options = $options;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * Whether or not associated URL should be registered to sitemap.
     *
     * @return bool
     */
    public function shouldBeRegistered(): bool
    {
        return $this->shouldBeRegistered;
    }

    /**
     * Allow URL registration to sitemap.
     */
    public function allowRegistration(): void
    {
        $this->shouldBeRegistered = true;
    }

    /**
     * Prevent URL registration to sitemap.
     */
    public function preventRegistration(): void
    {
        $this->shouldBeRegistered = false;
    }

    /**
     * URL that is about to be added to sitemap or NULL if not set yet.
     *
     * @return Url|null
     */
    public function getUrl(): ?Url
    {
        return $this->url;
    }

    /**
     * Set the URL that will be added to sitemap.
     *
     * @param Url $url Replacement
     */
    public function setUrl(Url $url): void
    {
        $this->url = $url;
    }

    /**
     * The route name.
     *
     * @return string
     */
    public function getRoute(): string
    {
        return $this->route;
    }

    /**
     * The sitemap route options.
     *
     * @return RouteOptions
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    public function getUrlGenerator(): UrlGeneratorInterface
    {
        if (!$this->urlGenerator) {
            throw new LogicException('UrlGenerator was not set.');
        }

        return $this->urlGenerator;
    }
}
