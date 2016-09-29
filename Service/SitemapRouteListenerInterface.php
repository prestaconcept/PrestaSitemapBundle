<?php

/*
 * This file is part of the prestaSitemapPlugin package.
 * (c) David Epely <depely@prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle\Service;

use \Presta\SitemapBundle\Event\SitemapRouteEvent;

/**
 * Inteface for sitemap route event listeners
 *
 * @author Mathieu Lemoine <mlemoine@mlemoine.name>
 */
interface SitemapRouteListenerInterface
{
    /**
     * Should check the route in the event and augment the Url as is appropriate
     *
     * @param SitemapRouteEvent $event
     */
    public function decorateUrl(SitemapRouteEvent $event);
}
