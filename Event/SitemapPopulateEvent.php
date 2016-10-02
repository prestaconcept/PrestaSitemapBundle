<?php

/**
 * This file is part of the PrestaSitemapBundle package.
 *
 * (c) PrestaConcept <www.prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle\Event;

use Presta\SitemapBundle\Service\UrlContainerInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Manage populate event
 *
 * @author depely
 */
class SitemapPopulateEvent extends Event
{
    /**
     * @Event("Presta\SitemapBundle\Event\SitemapPopulateEvent")
     */
    const ON_SITEMAP_POPULATE = 'presta_sitemap.populate';

    /**
     * @var UrlContainerInterface
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
     * @deprecated in favor of `Presta\SitemapBundle\Event\SitemapPopulateEvent::getUrlContainer()`
     *
     * @return UrlContainerInterface
     */
    public function getGenerator()
    {
        @trigger_error('getGenerator is deprecated since 1.5. Use getUrlContainer instead', E_USER_DEPRECATED);

        return $this->urlContainer;
    }

    /**
     * @return UrlContainerInterface
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
