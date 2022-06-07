<?php

/*
 * This file is part of the PrestaSitemapBundle package.
 *
 * (c) PrestaConcept <https://prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle\Routing;

use Symfony\Component\Routing\Route;

/**
 * Util class to parse sitemap option value from Route objects.
 *
 * @phpstan-type RouteOptions array{
 *     section: string|null,
 *     lastmod: \DateTimeInterface|null,
 *     changefreq: string|null,
 *     priority: float|string|int|null
 * }
 */
final class RouteOptionParser
{
    /**
     * @param string $name
     * @param Route  $route
     *
     * @return RouteOptions|null
     */
    public static function parse(string $name, Route $route): ?array
    {
        $option = $route->getOption('sitemap');

        if ($option === null) {
            return null;
        }

        if (\is_string($option)) {
            if (!\function_exists('json_decode')) {
                throw new \RuntimeException(
                    \sprintf(
                        'The route %s sitemap options are defined as JSON string, but PHP extension is missing.',
                        $name
                    )
                );
            }
            $decoded = \json_decode($option, true);
            if (!\json_last_error() && \is_array($decoded)) {
                $option = $decoded;
            }
        }

        if (!\is_array($option) && !\is_bool($option)) {
            $bool = \filter_var($option, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

            if (null === $bool) {
                throw new \InvalidArgumentException(
                    \sprintf(
                        'The route %s sitemap option must be of type "boolean" or "array", got "%s"',
                        $name,
                        \gettype($option)
                    )
                );
            }

            $option = $bool;
        }

        if (!$option) {
            return null;
        }

        $options = [
            'section' => null,
            'lastmod' => null,
            'changefreq' => null,
            'priority' => null,
        ];
        if (\is_array($option)) {
            $options = \array_merge($options, $option);
        }

        if (\is_string($options['lastmod'])) {
            try {
                $lastmod = new \DateTimeImmutable($options['lastmod']);
            } catch (\Exception $e) {
                throw new \InvalidArgumentException(
                    \sprintf(
                        'The route %s has an invalid value "%s" specified for the "lastmod" option',
                        $name,
                        $options['lastmod']
                    ),
                    0,
                    $e
                );
            }

            $options['lastmod'] = $lastmod;
        }

        /** @var RouteOptions $options */

        return $options;
    }
}
