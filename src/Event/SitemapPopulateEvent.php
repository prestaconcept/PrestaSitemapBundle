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
use Presta\SitemapBundle\Service\UrlContainerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Event called whenever a sitemap build is requested.
 *
 * Subscribe to this event if :
 *  - you want to register non-static routes
 */
class SitemapPopulateEvent extends Event
{
    /**
     * @Event("Presta\SitemapBundle\Event\SitemapPopulateEvent")
     * @deprecated since presta/sitemap-bundle 3.3, use `SitemapPopulateEvent::class` instead.
     */
    public const ON_SITEMAP_POPULATE = 'presta_sitemap.populate';

    /**
     * @var UrlContainerInterface
     */
    protected $urlContainer;

    /**
     * Allows creating EventListeners for particular sitemap sections, used when dumping
     * @var string|null
     */
    protected $section;

    /**
     * @var UrlGeneratorInterface|null
     */
    protected $urlGenerator;

    /**
     * @param UrlContainerInterface      $urlContainer
     * @param string|null                $section
     * @param UrlGeneratorInterface|null $urlGenerator
     */
    public function __construct(
        UrlContainerInterface $urlContainer,
        string $section = null,
        UrlGeneratorInterface $urlGenerator = null
    ) {
        $this->urlContainer = $urlContainer;
        $this->section = $section;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @return UrlContainerInterface
     */
    public function getUrlContainer(): UrlContainerInterface
    {
        return $this->urlContainer;
    }

    /**
     * Section to be processed, null means any
     *
     * @return null|string
     */
    public function getSection(): ?string
    {
        return $this->section;
    }

    public function getUrlGenerator(): UrlGeneratorInterface
    {
        if (!$this->urlGenerator) {
            throw new LogicException('UrlGenerator was not set.');
        }

        return $this->urlGenerator;
    }
}
