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
 * Urlset which writes added URLs into (temporary) files directly, w/o consuming memory
 */
class DumpingUrlset extends Urlset
{
    /**
     * Temporary file holding the body of the sitemap
     * @var resource
     */
    private $bodyFile;

    /**
     * Saves prepared (in a temporary file) sitemap to target dir
     * Basename of sitemap location is used (as they should always match)
     *
     * @param string $targetDir Directory where file should be saved
     * @param bool   $gzip
     */
    public function save(string $targetDir, bool $gzip = false): void
    {
        $this->initializeFileHandler();
        $filename = realpath($targetDir) . '/' . basename($this->getLoc());
        $sitemapFile = fopen($filename, 'w+');
        if ($sitemapFile === false) {
            throw new \RuntimeException(
                \sprintf('Cannot open sitemap file %s for writing.', $filename)
            );
        }

        $structureXml = $this->getStructureXml();

        // since header may contain namespaces which may get added when adding URLs
        // we can't prepare the header beforehand, so here we just take it and add to the beginning of the file
        $header = (string)substr($structureXml, 0, (int)strpos($structureXml, 'URLS</urlset>'));
        fwrite($sitemapFile, $header);

        // append body file to sitemap file (after the header)
        fflush($this->bodyFile);
        fseek($this->bodyFile, 0);

        while (!feof($this->bodyFile)) {
            fwrite($sitemapFile, (string)fread($this->bodyFile, 65536));
        }
        fwrite($sitemapFile, '</urlset>');

        $streamInfo = stream_get_meta_data($this->bodyFile);
        fclose($this->bodyFile);
        // removing temporary file
        unlink($streamInfo['uri']);

        if ($gzip) {
            $this->loc .= '.gz';
            $filenameGz = $filename . '.gz';
            fseek($sitemapFile, 0);
            $sitemapFileGz = gzopen($filenameGz, 'wb9');
            if ($sitemapFileGz === false) {
                throw new \RuntimeException(
                    \sprintf('Cannot open sitemap gz file %s for writing.', $filenameGz)
                );
            }

            while (!feof($sitemapFile)) {
                gzwrite($sitemapFileGz, (string)fread($sitemapFile, 65536));
            }
            gzclose($sitemapFileGz);
        }

        fclose($sitemapFile);
        if ($gzip) {
            unlink($filename);
        }
    }

    /**
     * Append URL's XML (to temporary file)
     *
     * @param string $urlXml
     */
    protected function appendXML(string $urlXml): void
    {
        $this->initializeFileHandler();
        fwrite($this->bodyFile, $urlXml);
    }

    /**
     * @throws \RuntimeException
     */
    private function initializeFileHandler(): void
    {
        if (null !== $this->bodyFile) {
            return;
        }

        $tmpFile = tempnam(sys_get_temp_dir(), 'sitemap');
        if ($tmpFile === false) {
            throw new \RuntimeException('Cannot create temporary file');
        }

        $file = @fopen($tmpFile, 'w+');
        if ($file === false) {
            throw new \RuntimeException("Cannot create temporary file $tmpFile");
        }

        $this->bodyFile = $file;
    }
}
