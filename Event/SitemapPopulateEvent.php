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
use Symfony\Component\EventDispatcher\Event as BaseEvent;
use Symfony\Contracts\EventDispatcher\Event as ContractsBaseEvent;

if (is_subclass_of('Symfony\Component\EventDispatcher\EventDispatcher', 'Symfony\Contracts\EventDispatcher\EventDispatcherInterface')) {
    /**
     * Manage populate event
     *
     * @author depely
     */
    class SitemapPopulateEvent extends ContractsBaseEvent
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
} else {
    /**
     * Manage populate event
     *
     * @author depely
     */
    class SitemapPopulateEvent extends BaseEvent
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
}
