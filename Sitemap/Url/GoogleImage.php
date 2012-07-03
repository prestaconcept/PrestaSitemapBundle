<?php

/*
 * This file is part of the prestaSitemapPlugin package.
 * (c) David Epely <depely@prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle\Sitemap\Url;

/**
 * Class used for managing image's url entities
 * 
 * @author David Epely <depely@prestaconcept.net>
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
     * @return 	string
     */
    public function getLoc()
    {
        return $this->loc;
    }

    /**
     * @param 	string $caption
     */
    public function setCaption($caption)
    {
        $this->caption = $caption;
    }

    /**
     * @return 	string
     */
    public function getCaption()
    {
        return $this->caption;
    }

    /**
     * @param 	string $caption
     */
    public function setGeoLocation($geo_location)
    {
        $this->geo_location = $geo_location;
    }

    /**
     * @return 	string
     */
    public function getGeoLocation()
    {
        return $this->geo_location;
    }

    /**
     * @param 	string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return 	string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param 	string $license
     */
    public function setLicense($license)
    {
        $this->license = $license;
    }

    /**
     * @return 	string
     */
    public function getLicense()
    {
        return $this->license;
    }

    /**
     * Return the xml representation for the image
     * 
     * @return 	string
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
