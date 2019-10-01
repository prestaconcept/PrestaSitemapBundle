<?php

/**
 * This file is part of the PrestaSitemapBundle package.
 *
 * (c) PrestaConcept <www.prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle\Service;

/**
 * Interface for class that intend to dump a sitemap.
 *
 * @author Yann Eugoné <yeugone@prestaconcept.net>
 */
interface DumperInterface extends UrlContainerInterface
{
    /**
     * Dumps sitemaps and sitemap index into provided directory
     *
     * @param string|null $targetDir Directory where to save sitemap files
     * @param string|null $host      The current host base URL
     * @param string|null $section   Optional section name - only sitemaps of this section will be updated
     * @param array       $options   Possible options: gzip
     *
     * @return array|bool
     */
    public function dump($targetDir, $host, $section = null, array $options = []);
}
