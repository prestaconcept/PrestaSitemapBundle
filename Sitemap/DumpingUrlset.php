<?php

/*
 * This file is part of the prestaSitemapPlugin package.
 * (c) David Epely <depely@prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle\Sitemap;

/**
 * Urlset which writes added URLs into (temporary) files directly, w/o consuming memory
 *
 * @author Konstantin Tjuterev <kostik.lv@gmail.com>
 */
class DumpingUrlset extends Urlset
{
    /**
     * Temporary file holding the body of the sitemap
     *
     * @var \SplTempFileObject
     */
    private $bodyFile;

    /**
     * @param string    $loc      This Urlset (sitemap) URL, for use in Sitemapindex
     * @param \DateTime $lastmod  Modification time
     */
    public function __construct($loc, \DateTime $lastmod = null)
    {
        parent::__construct($loc, $lastmod);
        $this->bodyFile = new \SplTempFileObject(0); // Use disk, not memory
    }

    /**
     * Append URL's XML (to temporary file)
     *
     * @param $urlXml
     */
    protected function appendXML($urlXml)
    {
        $this->bodyFile->fwrite($urlXml);
    }

    /**
     * Saves prepared (in a temporary file) sitemap to target dir
     * Basename of sitemap location is used (as they should always match)
     *
     * @param $targetDir Directory where file should be saved
     */
    public function save($targetDir)
    {
        $filename = realpath($targetDir) . '/' . basename($this->getLoc());
        $sitemapFile = new \SplFileObject($filename, 'w');
        $structureXml = $this->getStructureXml();

        // since header may contain namespaces which may get added when adding URLs
        // we can't prepare the header beforehand, so here we just take it and add to the beginning of the file
        $header = substr($structureXml, 0, strpos($structureXml, 'URLS</urlset>'));
        $sitemapFile->fwrite($header);

        // append body file to sitemap file (after the header)
        $this->bodyFile->fflush();
        $this->bodyFile->rewind();
        while (!$this->bodyFile->eof()) {
            $sitemapFile->fwrite($this->bodyFile->fgets());
        }
        $sitemapFile->fwrite('</urlset>');
        $sitemapFile->fflush();
    }
}