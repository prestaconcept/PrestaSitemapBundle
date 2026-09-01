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

    /**
     * Encode URL
     *
     * @param string $string
     *
     * @return string
     */
    public static function encodeUrl(string $url): string
    {
        $parts = parse_url($url);

        // Optional but we only sanitize URLs with scheme and host defined
        if ($parts === false || empty($parts['scheme']) || empty($parts['host'])) {
            return $url;
        }

        $sanitizedPath = null;
        if (!empty($parts['path'])) {
            $pathParts = explode('/', $parts['path']);
            foreach ($pathParts as $pathPart) {
                if (empty($pathPart)) {
                    continue;
                }
                // The Path part might already be urlencoded
                $sanitizedPath .= '/'.rawurlencode(rawurldecode($pathPart));
            }
        }

        // Build the url
        $targetUrl = $parts['scheme'].'://'.
            ((!empty($parts['user']) && !empty($parts['pass'])) ? $parts['user'].':'.$parts['pass'].'@' : '').
            $parts['host'].
            (!empty($parts['port']) ? ':'.$parts['port'] : '').
            (!empty($sanitizedPath) ? $sanitizedPath : '').
            (!empty($parts['query']) ? '?'.self::encode($parts['query']) : '').
            (!empty($parts['fragment']) ? '#'.$parts['fragment'] : '');

        return $targetUrl;
    }
}
