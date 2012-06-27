<?php
namespace Presta\SitemapBundle\Sitemap;

/**
 * Description of XmlConstraint
 *
 * @author depely
 */
abstract class XmlConstraint
{
    const LIMIT_ITEMS   = 49999;
    const LIMIT_BYTES   = 10000000; // 10,485,760 bytes - 485,760
    
    protected $limitItemsReached    = false;
    protected $limitBytesReached    = false;
    protected $countBytes           = 0;
    protected $countItems           = 0;
    
    /**
     * @return bool
     */
    public function isFull()
    {
        return $this->limitItemsReached || $this->limitBytesReached;
    }
    
    /**
     * Render full and valid xml 
     */
    abstract function toXml();
    
}
