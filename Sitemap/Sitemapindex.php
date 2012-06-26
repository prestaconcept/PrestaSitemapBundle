<?php 

namespace Presta\SitemapBundle\Sitemap;


/**
 * Manage generation of groups of urls
 * 
 * @author  David Epely
 */
class Sitemapindex extends XmlConstraint
{
    protected $urlsets = array();
    
    public function addSitemap($name, Urlset $urlset)
    {
        if ($this->isFull()) {
            throw new \RuntimeException('The sitemapindex limit has been exceeded');
        }
        
        $this->urlsets[$name] = $urlset;
        
        //---------------------
        //Check limits 
        if (count($this->urlsets) >= self::LIMIT_NUMBER) {
           $this->limitNumberReached = true;
        }
        
        $sitemapLength = strlen($this->getSitemapXml($urlset));
        $this->countBytes += $sitemapLength;
        
        if ($this->countBytes + $sitemapLength + strlen($this->getStructureXml()) > self::LIMIT_BYTES ) {
            //we suppose the next sitemap is almost the same length and cannot be added
            //plus we keep 500kB (@see self::LIMIT_BYTES)
            $this->limitByteReached = true;
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
        
        $sitemaps = '';
        
        foreach ($this->getUrlsets() as $urlset) {
            $sitemaps .= $this->getSitemapXml($urlset);
        }
        
        return str_replace('SITEMAPS', $sitemaps, $xml);
    }
}