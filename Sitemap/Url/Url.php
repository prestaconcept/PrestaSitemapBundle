<?php

namespace Presta\SitemapBundle\Sitemap\Url;

/**
 * Description of UrlInterface
 *
 * @author depely
 */
interface Url 
{
    /**
     * render element as xml 
     * @return string
     */
    public function toXml();
    
    /**
     * list of used namespaces
     * @return array - [{ns} => {location}]
     */
    public function getCustomNamespaces();
}
