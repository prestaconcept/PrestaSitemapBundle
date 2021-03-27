<?php

/*
 * This file is part of the PrestaSitemapBundle package.
 *
 * (c) PrestaConcept <https://prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle\Service;

/**
 * Interface for class that intend to dump a sitemap.
 */
interface DumperInterface extends UrlContainerInterface
{
    /**
     * Dumps sitemaps and sitemap index into provided directory
     *
     * @param string               $targetDir Directory where to save sitemap files
     * @param string               $host      The current host base URL
     * @param string|null          $section   Optional section name - only sitemaps of this section will be updated
     * @param array<string, mixed> $options   Possible options: gzip
     *
     * @return array<int, string>|bool
     */
    public function dump(string $targetDir, string $host, string $section = null, array $options = []);
}
