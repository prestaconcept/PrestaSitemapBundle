<?php

/*
 * This file is part of the PrestaSitemapBundle package.
 *
 * (c) PrestaConcept <https://prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle\Sitemap\Url;

use Presta\SitemapBundle\Sitemap\Utils;

/**
 * Sitemap image object attached to a sitemap Url.
 *
 * https://developers.google.com/search/docs/advanced/sitemaps/image-sitemaps
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
    public function __construct(
        string $location,
        string $caption = null,
        string $geoLocation = null,
        string $title = null,
        string $license = null
    ) {
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
    public function setLocation(string $location): self
    {
        $this->location = $location;

        return $this;
    }

    /**
     * @return string
     */
    public function getLocation(): string
    {
        return $this->location;
    }

    /**
     * @param null|string $caption
     *
     * @return GoogleImage
     */
    public function setCaption(?string $caption): self
    {
        $this->caption = $caption;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getCaption(): ?string
    {
        return $this->caption;
    }

    /**
     * @param null|string $geoLocation
     *
     * @return GoogleImage
     */
    public function setGeoLocation(?string $geoLocation): self
    {
        $this->geoLocation = $geoLocation;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getGeoLocation(): ?string
    {
        return $this->geoLocation;
    }

    /**
     * @param null|string $title
     *
     * @return GoogleImage
     */
    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param null|string $license
     *
     * @return GoogleImage
     */
    public function setLicense(?string $license): self
    {
        $this->license = $license;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getLicense(): ?string
    {
        return $this->license;
    }

    /**
     * Return the xml representation for the image
     *
     * @return string
     */
    public function toXML(): string
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
