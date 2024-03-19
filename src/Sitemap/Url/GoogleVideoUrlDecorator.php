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

use Presta\SitemapBundle\Exception;

/**
 * Url decorator to add videos to a sitemap Url.
 *
 * https://developers.google.com/search/docs/advanced/sitemaps/video-sitemaps
 */
class GoogleVideoUrlDecorator extends UrlDecorator
{
    public const LIMIT_ITEMS = 1000;

    /**
     * @var array<string, string>
     */
    protected $customNamespaces = ['video' => 'http://www.google.com/schemas/sitemap-video/1.1'];

    /**
     * @var string
     */
    protected $videoXml = '';

    /**
     * @var bool
     */
    protected $limitItemsReached = false;

    /**
     * @var int
     */
    protected $countItems = 0;

    /**
     * @param GoogleVideo $video
     *
     * @return GoogleVideoUrlDecorator
     */
    public function addVideo(GoogleVideo $video): self
    {
        if ($this->isFull()) {
            throw new Exception\GoogleVideoException('The video limit has been exceeded');
        }

        $this->videoXml .= $video->toXml();

        //---------------------
        //Check limits
        if (++$this->countItems >= self::LIMIT_ITEMS) {
            $this->limitItemsReached = true;
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function toXml(): string
    {
        $baseXml = $this->urlDecorated->toXml();

        return str_replace('</url>', $this->videoXml . '</url>', $baseXml);
    }

    /**
     * @return bool
     */
    public function isFull(): bool
    {
        return $this->limitItemsReached;
    }
}
