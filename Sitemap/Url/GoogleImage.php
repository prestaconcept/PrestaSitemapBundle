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
    protected $loc;

    /**
     * @var string|null
     */
    protected $caption;

    /**
     * @var string|null
     */
    protected $geo_location;

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
     * @param string      $loc
     * @param string|null $caption      [optional]
     * @param string|null $geo_location [optional]
     * @param string|null $title        [optional]
     * @param string|null $license      [optional]
     */
    public function __construct(
        string $loc,
        string $caption = null,
        string $geo_location = null,
        string $title = null,
        string $license = null
    ) {
        $this->setLoc($loc);
        $this->setCaption($caption);
        $this->setGeoLocation($geo_location);
        $this->setTitle($title);
        $this->setLicense($license);
    }

    /**
     * @param string $loc
     *
     * @return GoogleImage
     */
    public function setLoc(string $loc): GoogleImage
    {
        $this->loc = $loc;

        return $this;
    }

    /**
     * @return string
     */
    public function getLoc(): string
    {
        return $this->loc;
    }

    /**
     * @param null|string $caption
     *
     * @return GoogleImage
     */
    public function setCaption(?string $caption): GoogleImage
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
     * @param null|string $geo_location
     *
     * @return GoogleImage
     */
    public function setGeoLocation(?string $geo_location): GoogleImage
    {
        $this->geo_location = $geo_location;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getGeoLocation(): ?string
    {
        return $this->geo_location;
    }

    /**
     * @param null|string $title
     *
     * @return GoogleImage
     */
    public function setTitle(?string $title): GoogleImage
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
    public function setLicense(?string $license): GoogleImage
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

        $xml .= '<image:loc>' . Utils::encode($this->getLoc()) . '</image:loc>';

        if ($this->getCaption()) {
            $xml .= '<image:caption>' . Utils::render($this->getCaption()) . '</image:caption>';
        }

        if ($this->getGeoLocation()) {
            $xml .= '<image:geo_location>' . Utils::render($this->getGeoLocation()) . '</image:geo_location>';
        }

        if ($this->getTitle()) {
            $xml .= '<image:title>' . Utils::render($this->getTitle()) . '</image:title>';
        }

        if ($this->getLicense()) {
            $xml .= '<image:license>' . Utils::render($this->getLicense()) . '</image:license>';
        }

        $xml .= '</image:image>';

        return $xml;
    }
}
