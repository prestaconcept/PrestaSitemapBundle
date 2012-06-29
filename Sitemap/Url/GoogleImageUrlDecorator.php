<?php
namespace Presta\SitemapBundle\Sitemap\Url;

use Presta\SitemapBundle\Exception;

/**
 * @author David Epely 
 */
class GoogleImageUrlDecorator extends UrlDecorator
{
    const LIMIT_ITEMS = 1000;
    
    protected $imageXml = '';
    protected $customNamespaces = array('image' => 'http://www.google.com/schemas/sitemap-image/1.1');
    
    protected $limitItemsReached = false;
    protected $countItems = 0;
    
    public function addImage(GoogleImage $image)
    {
        if ($this->isFull()) {
            throw new Exception\GoogleImageUrlDecorator('The image limit has been exceeded');
        }
        
        $this->imageXml .= $image->toXml();
        
        //---------------------
        //Check limits 
        if ($this->countItems++ >= self::LIMIT_ITEMS) {
           $this->limitItemsReached = true;
        }
        //---------------------
    }
    
    /**
     * add image elements before the closing tag
     * 
     * @return string 
     */
    public function toXml()
    {
        $baseXml = $this->urlDecorated->toXml();
        return str_replace('</url>', $this->imageXml . '</url>', $baseXml);
    }
    
    /**
     * @return bool
     */
    public function isFull()
    {
        return $this->limitItemsReached;
    }
}