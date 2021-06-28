<?php

/*
 * This file is part of the PrestaSitemapBundle package.
 *
 * (c) PrestaConcept <https://prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle\Sitemap;

/**
 * XML utils for sitemap format.
 */
class Utils
{
    /**
     * Wrap string with CDATA markup
     *
     * @param string|null $string
     *
     * @return string
     */
    public static function cdata(?string $string): string
    {
        return '<![CDATA[' . $string . ']]>';
    }

    /**
     * Encode string with html special chars
     *
     * @param string $string
     *
     * @return string
     */
    public static function encode(string $string): string
    {
        return htmlspecialchars($string, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
