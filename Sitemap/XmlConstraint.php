<?php
namespace Presta\SitemapBundle\Sitemap;

/**
 * Description of XmlConstraint
 *
 * @author depely
 */
abstract class XmlConstraint
{
    const LIMIT_NUMBER  = 49999;
    const LIMIT_BYTES   = 10000000; // 10,485,760 bytes - 485,760
    
    protected $limitNumberReached     = false;
    protected $limitByteReached       = false;
    protected $countBytes             = 0;
    
    /**
     * @return bool
     */
    public function isFull()
    {
        return $this->limitNumberReached || $this->limitByteReached;
    }
    
    
    
}
