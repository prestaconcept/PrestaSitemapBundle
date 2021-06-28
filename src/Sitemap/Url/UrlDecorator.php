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

/**
 * Base class for all Url decorators.
 */
abstract class UrlDecorator implements Url
{
    /**
     * @var Url
     */
    protected $urlDecorated;

    /**
     * @var array<string, string>
     */
    protected $customNamespaces = [];

    /**
     * @param Url $urlDecorated
     */
    public function __construct(Url $urlDecorated)
    {
        $this->urlDecorated = $urlDecorated;
    }

    /**
     * @inheritdoc
     */
    public function getCustomNamespaces(): array
    {
        return array_merge($this->urlDecorated->getCustomNamespaces(), $this->customNamespaces);
    }

    /**
     * @return Url
     */
    public function getUrlDecorated(): Url
    {
        return $this->urlDecorated;
    }
}
