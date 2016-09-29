<?php

/*
 * This file is part of the prestaSitemapPlugin package.
 * (c) David Epely <depely@prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle\Event;

use Symfony\Component\EventDispatcher\Event;

use Presta\SitemapBundle\Sitemap\Url\Url;
use Presta\SitemapBundle\Sitemap\Url\UrlDecorator;

/**
 * Manage populate event
 *
 * @author Mathieu Lemoine <mlemoine@mlemoine.name>
 */
class SitemapRouteEvent extends Event
{
    const ON_SITEMAP_ROUTE = 'presta_sitemap.route';

    /**
     * @var Url
     */
    protected $url;

    /**
     * @var string
     */
    protected $name;

    /**
     * @ar mixed
     */
    protected $options;
    
    /**
     * @param Url    $urlContainer
     * @param string $name
     * @param mixed  $options
     */
    public function __construct(Url $url, $name, $options)
    {
        $this->url     = $url;
        $this->name    = $name;
        $this->options = $options;
    }

    /**
     * @return Url
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Replace the original Url by a decorated version of it.
     *
     * @param UrlDecorator $url
     */
    public function setDecoratedUrl(UrlDecorator $url)
    {
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getOptions()
    {
        return $this->options;
    }
}
