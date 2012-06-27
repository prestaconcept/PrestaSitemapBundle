<?php 

namespace Presta\SitemapBundle\Sitemap;


/**
 * Manage sitemaps listing
 * 
 * @author  David Epely
 */
class Sitemapindex extends XmlConstraint
{
    protected $sitemapsXml      = '';
    
    public function addSitemap(Urlset $urlset)
    {
        if ($this->isFull()) {
            throw new \RuntimeException('The sitemapindex limit has been exceeded');
        }
        
        $sitemapXml = $this->getSitemapXml($urlset);
        $this->sitemapsXml .= $sitemapXml;
        
        //---------------------
        //Check limits 
        if ($this->countItems++ >= self::LIMIT_ITEMS) {
           $this->limitItemsReached = true;
        }
        
        
        $sitemapLength = strlen($sitemapXml);
        $this->countBytes += $sitemapLength;
        
        if ($this->countBytes + $sitemapLength + strlen($this->getStructureXml()) > self::LIMIT_BYTES ) {
            //we suppose the next sitemap is almost the same length and cannot be added
            //plus we keep 500kB (@see self::LIMIT_BYTES)
            $this->limitBytesReached = true;
        }
        //---------------------
    }
    
    public function getUrlsets()
    {
        return $this->urlsets;
    }
    
    protected function getSitemapXml(Urlset $urlset)
    {
        return '<sitemap><loc>' . $urlset->getLoc() 
                . '</loc><lastmod>' . $urlset->getLastmod()->format('c') 
                . '</lastmod></sitemap>';
    }
    
    protected function getStructureXml()
    {
        $struct = '<?xml version="1.0" encoding="UTF-8"?>';
        $struct .= '<sitemapindex xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"';
        $struct .= ' xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/siteindex.xsd"';
        $struct .= ' xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">SITEMAPS</sitemapindex>';
        
        return $struct;
    }
    
    public function toXml()
    {
        $xml = $this->getStructureXml();
        
        return str_replace('SITEMAPS', $this->sitemapsXml, $xml);
    }
}