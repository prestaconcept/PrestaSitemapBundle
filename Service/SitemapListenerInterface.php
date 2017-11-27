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

/**
 * Inteface for sitemap event listeners
 *
 * @author Konstantin Tjuterev <kostik.lv@gmail.com>
 *
 * @deprecated This interface has been deprecated in favor of Symfony standard event listener and subscriber.
 *             Please see documentation if you are in trouble.
 *             To be removed in next major release : 2.0
 */
interface SitemapListenerInterface
{
    /**
     * Should check $event->getSection() and then populate the sitemap
     * using $event->getUrlContainer()->addUrl(\Presta\SitemapBundle\Sitemap\Url\Url $url, $section)
     * if $event->getSection() is null or matches the listener's section
     *
     * @param SitemapPopulateEvent $event
     */
    public function populateSitemap(SitemapPopulateEvent $event);
}
