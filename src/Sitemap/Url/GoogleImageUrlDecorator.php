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
 * Url decorator to add images to a sitemap Url.
 *
 * https://developers.google.com/search/docs/advanced/sitemaps/image-sitemaps
 */
class GoogleImageUrlDecorator extends UrlDecorator
{
    public const LIMIT_ITEMS = 1000;

    /**
     * @var string
     */
    protected $imageXml = '';

    /**
     * @var array<string, string>
     */
    protected $customNamespaces = ['image' => 'http://www.google.com/schemas/sitemap-image/1.1'];

    /**
     * @var bool
     */
    protected $limitItemsReached = false;

    /**
     * @var int
     */
    protected $countItems = 0;

    /**
     * @param GoogleImage $image
     *
     * @return GoogleImageUrlDecorator
     */
    public function addImage(GoogleImage $image): self
    {
        if ($this->isFull()) {
            throw new Exception\GoogleImageException('The image limit has been exceeded');
        }

        $this->imageXml .= $image->toXml();

        //---------------------
        //Check limits
        if ($this->countItems++ >= self::LIMIT_ITEMS) {
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

        return str_replace('</url>', $this->imageXml . '</url>', $baseXml);
    }

    /**
     * @return bool
     */
    public function isFull(): bool
    {
        return $this->limitItemsReached;
    }
}
