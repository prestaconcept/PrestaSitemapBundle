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

    public function __get($name)
    {
        $map = [
            'loc' => 'location',
            'geo_location' => 'geoLocation',
        ];

        if (array_key_exists($name, $map)) {
            $newName = $map[$name];
            @trigger_error(
                sprintf('Property %s::$%s is deprecated since 2.3.0, use $%s instead.', __CLASS__, $name, $newName),
                E_USER_DEPRECATED
            );

            return $this->{$newName};
        }

        trigger_error(sprintf('Undefined property: %s::$%s', __CLASS__, $name), E_NOTICE);

        return null;
    }

    public function __set($name, $value)
    {
        $map = [
            'loc' => 'location',
            'geo_location' => 'geoLocation',
        ];

        if (array_key_exists($name, $map)) {
            $newName = $map[$name];
            @trigger_error(
                sprintf('Property %s::$%s is deprecated since 2.3.0, use $%s instead.', __CLASS__, $name, $newName),
                E_USER_DEPRECATED
            );

            $this->{$newName} = $value;

            return;
        }

        trigger_error(sprintf('Undefined property: %s::$%s', __CLASS__, $name), E_NOTICE);
    }

    /**
     * @deprecated since 2.3.0, to be removed in 3.0.0
     *
     * @param string $loc
     *
     * @return GoogleImage
     */
    public function setLoc($loc)
    {
        @trigger_error(
            sprintf('Method %s is deprecated since 2.3.0, use %s::setLocation instead.', __METHOD__, __CLASS__),
            E_USER_DEPRECATED
        );
        $this->setLocation($loc);

        return $this;
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
     * @deprecated since 2.3.0, to be removed in 3.0.0
     *
     * @return string
     */
    public function getLoc()
    {
        @trigger_error(
            sprintf('Method %s is deprecated since 2.3.0, use %s::getLocation instead.', __METHOD__, __CLASS__),
            E_USER_DEPRECATED
        );

        return $this->getLocation();
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
