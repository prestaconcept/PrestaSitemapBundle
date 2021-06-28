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
 * Url decorator to add alternatives to a sitemap Url.
 *
 * https://developers.google.com/search/docs/advanced/crawling/localized-versions
 */
class GoogleMultilangUrlDecorator extends UrlDecorator
{
    public const REL_ALTERNATE = 'alternate';

    /**
     * @var array<string, string>
     */
    protected $customNamespaces = ['xhtml' => 'http://www.w3.org/1999/xhtml'];

    /**
     * @var string
     */
    protected $linkXml = '';

    /**
     * add an alternative language to the url
     *
     * @param string      $href     Valid url of the translated page
     * @param string      $hreflang Valid language code @see
     *                              http://www.w3.org/TR/xhtml-modularization/abstraction.html#dt_LanguageCode
     * @param string|null $rel      (default is alternate) - valid link type @see
     *                              http://www.w3.org/TR/xhtml-modularization/abstraction.html#dt_LinkTypes
     *
     * @return GoogleMultilangUrlDecorator
     */
    public function addLink(string $href, string $hreflang, string $rel = null): self
    {
        $this->linkXml .= $this->generateLinkXml($href, $hreflang, $rel);

        return $this;
    }

    /**
     * @param string      $href
     * @param string      $hreflang
     * @param string|null $rel
     *
     * @return string
     */
    protected function generateLinkXml(string $href, string $hreflang, string $rel = null): string
    {
        if (null == $rel) {
            $rel = self::REL_ALTERNATE;
        }

        return '<xhtml:link rel="' . $rel
            . '" hreflang="' . $hreflang
            . '" href="' . Utils::encode($href) . '" />';
    }

    /**
     * @inheritdoc
     */
    public function toXml(): string
    {
        $baseXml = $this->urlDecorated->toXml();

        return str_replace('</url>', $this->linkXml . '</url>', $baseXml);
    }
}
