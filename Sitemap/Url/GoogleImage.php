<?php

namespace Presta\SitemapBundle\Sitemap\Url;

/**
 * Class used for managing image's url entites
 * 
 * @author David Epely
 * @author Alain Flaus <aflaus@prestaconcept.net>
 */
class GoogleImage
{
    protected $loc;
    protected $caption;
    protected $geo_location;
    protected $title;
    protected $license;

    /**
     * create a GoogleImage for your GoogleImageUrl
     * 
     * @param 	string $loc
     * @param 	string $caption[optional]
     * @param 	string $geo_location[optional]
     * @param 	string $title[optional]
     * @param 	string $license[optional]
     */
    public function __construct($loc, $caption = null, $geo_location = null, $title = null, $license = null)
    {
        $this->setLoc($loc);
        $this->setCaption($caption);
        $this->setGeoLocation($geo_location);
        $this->setTitle($title);
        $this->setLicense($license);
    }

    /**
     * @param 	string $internal_uri
     */
    public function setLoc($loc)
    {
        $this->loc = $loc;
    }

    /**
     * @return 	String
     */
    public function getLoc()
    {
        return $this->loc;
    }

    /**
     * @param 	String $caption
     */
    public function setCaption($caption)
    {
        $this->caption = $caption;
    }

    /**
     * @return 	String
     */
    public function getCaption()
    {
        return $this->caption;
    }

    /**
     * @param 	String $caption
     */
    public function setGeoLocation($geo_location)
    {
        $this->geo_location = $geo_location;
    }

    /**
     * @return 	String
     */
    public function getGeoLocation()
    {
        return $this->geo_location;
    }

    /**
     * @param 	String $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return 	String
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param 	String $license
     */
    public function setLicense($license)
    {
        $this->license = $license;
    }

    /**
     * @return 	String
     */
    public function getLicense()
    {
        return $this->license;
    }

    /**
     * Return the xml content of this object
     * 
     * @author	Alain Flaus <aflaus@prestaconcept.net>
     * @version	1.0 - 5 oct. 2010 - Alain Flaus <aflaus@prestaconcept.net>
     * @since	5 oct. 2010 - Alain Flaus <aflaus@prestaconcept.net>
     * @return 	String
     */
    public function toXML()
    {
        $xml = '<image:image><image:loc>' . $this->getLoc() . '</image:loc>';
        
        if ($this->getCaption()) {
            $xml .= '<image:caption>' . $this->getCaption() . '</image:caption>';
        }
        
        if ($this->getGeoLocation()) {
            $xml .= '<image:geo_location>' . $this->getGeoLocation() . '</image:geo_location>';
        }
        
        if ($this->getTitle()) {
            $xml .= '<image:title>' . $this->getTitle() . '</image:title>';
        }
        
        if ($this->getLicense()) {
            $xml .= '<image:license>' . $this->getLicense() . '</image:license>';
        }
        
        $xml .= '</image:image>';
                
        return $xml;
    }
}
