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

use Presta\SitemapBundle\Sitemap\Url\Url;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Event to allow generation of static routes sitemap urls.
 */
class SitemapAddUrlEvent extends Event
{
    /**
     * @Event("Presta\SitemapBundle\Event\SitemapAddUrlEvent")
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
     * @var array<string, mixed>
     */
    private $options;

    /**
     * @param string               $route
     * @param array<string, mixed> $options
     */
    public function __construct(string $route, array $options)
    {
        $this->route = $route;
        $this->options = $options;
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
     * @return array<string, mixed>
     */
    public function getOptions(): array
    {
        return $this->options;
    }
}
