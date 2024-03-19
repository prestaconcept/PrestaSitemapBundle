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

use DateTimeInterface;
use Presta\SitemapBundle\Sitemap\Utils;

/**
 * First class citizen of sitemaps.
 *
 * https://developers.google.com/search/docs/guides/create-URLs
 */
class UrlConcrete implements Url
{
    public const CHANGEFREQ_ALWAYS = 'always';
    public const CHANGEFREQ_HOURLY = 'hourly';
    public const CHANGEFREQ_DAILY = 'daily';
    public const CHANGEFREQ_WEEKLY = 'weekly';
    public const CHANGEFREQ_MONTHLY = 'monthly';
    public const CHANGEFREQ_YEARLY = 'yearly';
    public const CHANGEFREQ_NEVER = 'never';

    /**
     * @var string
     */
    protected $loc;

    /**
     * @var DateTimeInterface|null
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
     * @param string                 $loc        Absolute url
     * @param DateTimeInterface|null $lastmod    Last modification date
     * @param string|null            $changefreq Change frequency
     * @param float|string|int|null  $priority   Priority
     */
    public function __construct(
        string $loc,
        DateTimeInterface $lastmod = null,
        string $changefreq = null,
        $priority = null
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
    public function setLoc(string $loc): self
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
     * @param DateTimeInterface|null $lastmod
     *
     * @return UrlConcrete
     */
    public function setLastmod(?DateTimeInterface $lastmod): self
    {
        $this->lastmod = $lastmod;

        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getLastmod(): ?DateTimeInterface
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
    public function setChangefreq(string $changefreq = null): self
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
                    'The value "%s" is not supported by the option changefreq.' .
                    ' See http://www.sitemaps.org/protocol.html#xmlTagDefinitions',
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
    public function getChangefreq(): ?string
    {
        return $this->changefreq;
    }

    /**
     * Define the priority of this entry
     *
     * @param float|string|int|null $priority Define the priority
     *
     * @return UrlConcrete
     */
    public function setPriority($priority = null): self
    {
        if ($priority === null) {
            return $this;
        }

        if (is_string($priority) || is_int($priority)) {
            $priority = (float)$priority;
        }

        if (is_float($priority) && $priority >= 0 && $priority <= 1) {
            $this->priority = round($priority, 1);
        } else {
            throw new \RuntimeException(
                sprintf(
                    'The value "%s" is not supported by the option priority,' .
                    ' it must be a numeric between 0.0 and 1.0.' .
                    ' See http://www.sitemaps.org/protocol.html#xmlTagDefinitions',
                    $priority
                )
            );
        }

        return $this;
    }

    /**
     * @return float|null
     */
    public function getPriority(): ?float
    {
        return $this->priority;
    }

    /**
     * @inheritdoc
     */
    public function toXml(): string
    {
        $xml = '<url><loc>' . Utils::encode($this->getLoc()) . '</loc>';

        $lastmod = $this->getLastmod();
        if ($lastmod) {
            $xml .= '<lastmod>' . $lastmod->format('c') . '</lastmod>';
        }

        $changefreq = $this->getChangefreq();
        if ($changefreq) {
            $xml .= '<changefreq>' . $changefreq . '</changefreq>';
        }

        $priority = $this->getPriority();
        if ($priority) {
            $xml .= '<priority>' . number_format($priority, 1) . '</priority>';
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
