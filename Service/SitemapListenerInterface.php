<?php

/*
 * This file is part of the prestaSitemapPlugin package.
 * (c) David Epely <depely@prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle\Service;

use \Presta\SitemapBundle\Event\SitemapPopulateEvent;

/**
 * Inteface for sitemap event listeners
 *
 * @author Konstantin Tjuterev <kostik.lv@gmail.com>
 */
interface SitemapListenerInterface
{
    /**
     * Should check $event->getSection() and then populate the sitemap
     * using $event->getUrlContainer()->addUrl(\Presta\SitemapBundle\Sitemap\Url\Url $url, $section)
     * if $event->getSection() is null or matches the listener's section
     *
     * For each Url, a SitemapRouteEvent should be dispatched to let the chance to any third-party
     * to decorate the Url and add any suitable extension to the Sitemap.
     *
     * @param SitemapPopulateEvent $event
     */
    public function populateSitemap(SitemapPopulateEvent $event);
}
