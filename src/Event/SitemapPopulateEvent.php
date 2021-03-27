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

use Presta\SitemapBundle\Service\UrlContainerInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Manage populate event
 */
class SitemapPopulateEvent extends Event
{
    /**
     * @Event("Presta\SitemapBundle\Event\SitemapPopulateEvent")
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
     * @param UrlContainerInterface $urlContainer
     * @param string|null           $section
     */
    public function __construct(UrlContainerInterface $urlContainer, string $section = null)
    {
        $this->urlContainer = $urlContainer;
        $this->section = $section;
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
}
