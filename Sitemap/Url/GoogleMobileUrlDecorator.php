<?php
namespace Presta\SitemapBundle\Sitemap\Url;

use Presta\SitemapBundle\Exception;

/**
 * @author David Epely 
 */
class GoogleMobileUrlDecorator extends UrlDecorator
{
    protected $customNamespaces = array('mobile' => 'http://www.google.com/schemas/sitemap-mobile/1.0');
    
    /**
     * add image elements before the closing tag
     * 
     * @return string 
     */
    public function toXml()
    {
        $baseXml = $this->urlDecorated->toXml();
        return str_replace('</url>', '<mobile:mobile/></url>', $baseXml);
    }
}