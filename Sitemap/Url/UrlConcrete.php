<?php

/**
 * This file is part of the PrestaSitemapBundle
 *
 * (c) PrestaConcept <www.prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle\Sitemap\Url;

use Presta\SitemapBundle\Sitemap\Utils;

/**
 * Class used for managing url entities
 *
 * @author Christophe Dolivet
 * @author David Epely
 */
class UrlConcrete implements Url
{
    const CHANGEFREQ_ALWAYS = 'always';
    const CHANGEFREQ_HOURLY = 'hourly';
    const CHANGEFREQ_DAILY = 'daily';
    const CHANGEFREQ_WEEKLY = 'weekly';
    const CHANGEFREQ_MONTHLY = 'monthly';
    const CHANGEFREQ_YEARLY = 'yearly';
    const CHANGEFREQ_NEVER = 'never';

    protected $loc;
    protected $lastmod;
    protected $changefreq;
    protected $priority;

    /**
     * Construct a new basic url
     *
     * @param string $loc - absolute url
     * @param \DateTime $lastmod
     * @param string $changefreq
     * @param float $priority
     */
    public function __construct($loc, \DateTime $lastmod = null, $changefreq = null, $priority = null)
    {
        $this->setLoc($loc);
        $this->setLastmod($lastmod);
        $this->setChangefreq($changefreq);
        $this->setPriority($priority);
    }

    /**
     * @param string $loc
     */
    public function setLoc($loc)
    {
        $this->loc = $loc;
        return $this;
    }

    /**
     * @return string
     */
    public function getLoc()
    {
        return $this->loc;
    }

    /**
     * @param \DateTime $lastmod
     */
    public function setLastmod(\DateTime $lastmod = null)
    {
        $this->lastmod = $lastmod;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getLastmod()
    {
        return $this->lastmod;
    }

    /**
     * Define the change frequency of this entry
     *
     * @param string $changefreq - String or null value used for defining the change frequency
     */
    public function setChangefreq($changefreq = null)
    {
        if (!in_array(
            $changefreq,
            array(
                self::CHANGEFREQ_ALWAYS,
                self::CHANGEFREQ_HOURLY,
                self::CHANGEFREQ_DAILY,
                self::CHANGEFREQ_WEEKLY,
                self::CHANGEFREQ_MONTHLY,
                self::CHANGEFREQ_YEARLY,
                self::CHANGEFREQ_NEVER,
                null,
            )
        )) {
            throw new \RuntimeException(sprintf('The value "%s" is not supported by the option changefreq. See http://www.sitemaps.org/protocol.html#xmlTagDefinitions', $changefreq));
        }

        $this->changefreq = $changefreq;
        return $this;
    }

    /**
     * return the change frequency
     *
     * @return string
     */
    public function getChangefreq()
    {
        return $this->changefreq;
    }

    /**
     * Define the priority of this entry
     *
     * @param float $priority - Float or null value used for defining the priority
     */
    public function setPriority($priority = null)
    {
        if (!$priority) {
            return;
        }

        if ($priority && is_numeric($priority) && $priority >= 0 && $priority <= 1) {
            $this->priority = sprintf('%01.1f', $priority);
        } else {
            throw new \RuntimeException(sprintf('The value "%s" is not supported by the option priority, it must be a numeric between 0.0 and 1.0. See http://www.sitemaps.org/protocol.html#xmlTagDefinitions', $priority));
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * @return string
     */
    public function toXml()
    {
        $xml = '<url><loc>' . Utils::encode($this->getLoc()) . '</loc>';

        if ($this->getLastmod()) {
            $xml .= '<lastmod>' . $this->getLastmod()->format('c') . '</lastmod>';
        }

        if ($this->getChangefreq()) {
            $xml .= '<changefreq>' . $this->getChangefreq() . '</changefreq>';
        }

        if ($this->getPriority()) {
            $xml .= '<priority>' . $this->getPriority() . '</priority>';
        }

        $xml .= '</url>';

        return $xml;
    }

    /**
     * basic url has no namespace. see decorated urls
     * @return array
     */
    public function getCustomNamespaces()
    {
        return array();
    }
}
