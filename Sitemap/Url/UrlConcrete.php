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

use DateTime;
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

    /**
     * @var string
     */
    protected $loc;

    /**
     * @var DateTime|null
     */
    protected $lastmod;

    /**
     * @var string|null
     */
    protected $changefreq;

    /**
     * @var float|null
     */
    protected $priority;

    /**
     * Construct a new basic url
     *
     * @param string        $loc        Absolute url
     * @param DateTime|null $lastmod    Last modification date
     * @param string|null   $changefreq Change frequency
     * @param float|null    $priority   Priority
     */
    public function __construct(
        string $loc,
        DateTime $lastmod = null,
        string $changefreq = null,
        float $priority = null
    ) {
        $this->setLoc($loc);
        $this->setLastmod($lastmod);
        $this->setChangefreq($changefreq);
        $this->setPriority($priority);
    }

    /**
     * @param string $loc
     *
     * @return UrlConcrete
     */
    public function setLoc(string $loc): UrlConcrete
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
     * @param DateTime|null $lastmod
     *
     * @return UrlConcrete
     */
    public function setLastmod(DateTime $lastmod = null): UrlConcrete
    {
        $this->lastmod = $lastmod;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getLastmod()
    {
        return $this->lastmod;
    }

    /**
     * Define the change frequency of this entry
     *
     * @param string|null $changefreq Define the change frequency
     *
     * @return UrlConcrete
     */
    public function setChangefreq(string $changefreq = null): UrlConcrete
    {
        $frequencies = [
            self::CHANGEFREQ_ALWAYS,
            self::CHANGEFREQ_HOURLY,
            self::CHANGEFREQ_DAILY,
            self::CHANGEFREQ_WEEKLY,
            self::CHANGEFREQ_MONTHLY,
            self::CHANGEFREQ_YEARLY,
            self::CHANGEFREQ_NEVER,
            null,
        ];
        if (!in_array($changefreq, $frequencies)) {
            throw new \RuntimeException(
                sprintf(
                    'The value "%s" is not supported by the option changefreq. See http://www.sitemaps.org/protocol.html#xmlTagDefinitions',
                    $changefreq
                )
            );
        }

        $this->changefreq = $changefreq;

        return $this;
    }

    /**
     * return the change frequency
     *
     * @return string|null
     */
    public function getChangefreq()
    {
        return $this->changefreq;
    }

    /**
     * Define the priority of this entry
     *
     * @param float|null $priority Define the priority
     *
     * @return UrlConcrete
     */
    public function setPriority(float $priority = null): UrlConcrete
    {
        if (!$priority) {
            return $this;
        }

        if ($priority && is_numeric($priority) && $priority >= 0 && $priority <= 1) {
            $this->priority = number_format($priority, 1);
        } else {
            throw new \RuntimeException(
                sprintf(
                    'The value "%s" is not supported by the option priority, it must be a numeric between 0.0 and 1.0. See http://www.sitemaps.org/protocol.html#xmlTagDefinitions',
                    $priority
                )
            );
        }

        return $this;
    }

    /**
     * @return float|null
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * @inheritdoc
     */
    public function toXml(): string
    {
        $xml = '<url><loc>' . Utils::encode($this->getLoc()) . '</loc>';

        if ($this->getLastmod()) {
            $xml .= '<lastmod>' . $this->getLastmod()->format('c') . '</lastmod>';
        }

        if ($this->getChangefreq()) {
            $xml .= '<changefreq>' . $this->getChangefreq() . '</changefreq>';
        }

        if ($this->getPriority()) {
            $xml .= '<priority>' . number_format($this->getPriority(), 1) . '</priority>';
        }

        $xml .= '</url>';

        return $xml;
    }

    /**
     * @inheritdoc
     */
    public function getCustomNamespaces(): array
    {
        return []; // basic url has no namespace. see decorated urls
    }
}
