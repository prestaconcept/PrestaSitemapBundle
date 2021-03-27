<?php

/*
 * This file is part of the PrestaSitemapBundle package.
 *
 * (c) PrestaConcept <https://prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle\Tests\Unit;

use Presta\SitemapBundle\Service\UrlContainerInterface;
use Presta\SitemapBundle\Sitemap\Url\Url;

/**
 * For tests purpose only !
 * Add urls to internal instance var.
 */
final class InMemoryUrlContainer implements UrlContainerInterface
{
    /**
     * @var Url[][]
     */
    private $urls = [];

    public function addUrl(Url $url, string $section): void
    {
        $this->urls[$section][] = $url;
    }

    /**
     * @param string $section
     *
     * @return Url[]
     */
    public function getUrlset(string $section): array
    {
        return $this->urls[$section] ?? [];
    }

    /**
     * @return string[]
     */
    public function getSections(): array
    {
        return \array_keys($this->urls);
    }
}
