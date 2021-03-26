<?php

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

    public function addUrl(Url $url, $section)
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
