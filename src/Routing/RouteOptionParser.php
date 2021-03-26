<?php

namespace Presta\SitemapBundle\Routing;

use Symfony\Component\Routing\Route;

final class RouteOptionParser
{
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
                        $option
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
                $options['lastmod'] = new \DateTimeImmutable($options['lastmod']);
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
        }

        return $options;
    }
}
