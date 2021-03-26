<?php

/**
 * This file is part of the PrestaSitemapBundle package.
 *
 * (c) PrestaConcept <www.prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle\Sitemap\Url;

use Presta\SitemapBundle\Sitemap\Utils;

/**
 * Class used for managing image's url entities
 *
 * @author David Epely <depely@prestaconcept.net>
 * @author Alain Flaus <aflaus@prestaconcept.net>
 */
class GoogleImage
{
    /**
     * @var string
     */
    protected $location;

    /**
     * @var string|null
     */
    protected $caption;

    /**
     * @var string|null
     */
    protected $geoLocation;

    /**
     * @var string|null
     */
    protected $title;

    /**
     * @var string|null
     */
    protected $license;

    /**
     * create a GoogleImage for your GoogleImageUrl
     *
     * @param string      $location
     * @param string|null $caption     [optional]
     * @param string|null $geoLocation [optional]
     * @param string|null $title       [optional]
     * @param string|null $license     [optional]
     */
    public function __construct($location, $caption = null, $geoLocation = null, $title = null, $license = null)
    {
        $this->setLocation($location);
        $this->setCaption($caption);
        $this->setGeoLocation($geoLocation);
        $this->setTitle($title);
        $this->setLicense($license);
    }

    /**
     * @param string $location
     *
     * @return GoogleImage
     */
    public function setLocation($location)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param null|string $caption
     *
     * @return GoogleImage
     */
    public function setCaption($caption)
    {
        $this->caption = $caption;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getCaption()
    {
        return $this->caption;
    }

    /**
     * @param null|string $geoLocation
     *
     * @return GoogleImage
     */
    public function setGeoLocation($geoLocation)
    {
        $this->geoLocation = $geoLocation;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getGeoLocation()
    {
        return $this->geoLocation;
    }

    /**
     * @param null|string $title
     *
     * @return GoogleImage
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param null|string $license
     *
     * @return GoogleImage
     */
    public function setLicense($license)
    {
        $this->license = $license;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getLicense()
    {
        return $this->license;
    }

    /**
     * Return the xml representation for the image
     *
     * @return string
     */
    public function toXML()
    {
        $xml = '<image:image>';

        $xml .= '<image:loc>' . Utils::encode($this->getLocation()) . '</image:loc>';

        if ($this->getCaption()) {
            $xml .= '<image:caption>' . Utils::cdata($this->getCaption()) . '</image:caption>';
        }

        if ($this->getGeoLocation()) {
            $xml .= '<image:geo_location>' . Utils::cdata($this->getGeoLocation()) . '</image:geo_location>';
        }

        if ($this->getTitle()) {
            $xml .= '<image:title>' . Utils::cdata($this->getTitle()) . '</image:title>';
        }

        if ($this->getLicense()) {
            $xml .= '<image:license>' . Utils::cdata($this->getLicense()) . '</image:license>';
        }

        $xml .= '</image:image>';

        return $xml;
    }
}
