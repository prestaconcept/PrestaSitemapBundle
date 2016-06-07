<?php

/*
 * This file is part of the prestaSitemapPlugin package.
 * (c) David Epely <depely@prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle\Event;

use Presta\SitemapBundle\Service\GeneratorInterface;
use Presta\SitemapBundle\Service\UrlContainerInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Manage populate event
 *
 * @author depely
 */
class SitemapPopulateEvent extends Event
{
    const ON_SITEMAP_POPULATE = 'presta_sitemap.populate';

    /**
     * @var GeneratorInterface
     */
    protected $urlContainer;

    /**
     * Allows creating EventListeners for particular sitemap sections, used when dumping
     * @var string
     */
    protected $section;

    /**
     * @param UrlContainerInterface $urlContainer
     * @param string|null           $section
     */
    public function __construct(UrlContainerInterface $urlContainer, $section = null)
    {
        $this->urlContainer = $urlContainer;
        $this->section = $section;
    }

    /**
     * @return GeneratorInterface
     */
    public function getUrlContainer()
    {
        return $this->urlContainer;
    }

    /**
     * Section to be processed, null means any
     *
     * @return null|string
     */
    public function getSection()
    {
        return $this->section;
    }
}
