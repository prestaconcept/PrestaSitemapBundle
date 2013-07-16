<?php

/*
 * This file is part of the prestaSitemapPlugin package.
 * (c) David Epely <depely@prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle\Service;

use Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

/**
 * Service for dumping sitemaps into static files
 *
 * @author Konstantin Tjuterev <kostik.lv@gmail.com>
 */
class Dumper extends Generator
{
    /**
     * Path to folder where temporary files will be created
     *
     * @var string
     */
    protected $tmpFolder;

    /**
     * Base URL where dumped sitemap files can be accessed (we can't guess that from console)
     *
     * @var string
     */
    protected $baseUrl;

    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    protected $filesystem;

    /**
     * @param \Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher $dispatcher Symfony's EventDispatcher
     * @param \Symfony\Component\Filesystem\Filesystem                         $filesystem Symfony's Filesystem
     */
    public function __construct(ContainerAwareEventDispatcher $dispatcher, Filesystem $filesystem)
    {
        $this->dispatcher = $dispatcher;
        $this->filesystem = $filesystem;
    }

    /**
     * Dumps sitemaps and sitemap index into provided directory
     *
     * @param string $targetDir Directory where to save sitemap files
     * @param null   $section   Optional section name - only sitemaps of this section will be updated
     *
     * @return array|bool
     */
    public function dump($targetDir, $host, $section = null)
    {
        $this->baseUrl = $host;
        // we should prepare temp folder each time, because dump may be called several times (with different sections)
        // and activate command below removes temp folder
        $this->prepareTempFolder();

        $this->populate($section);

        // if root wasn't created during populating
        // it means no URLs were added to the sitemap
        if (!$this->root) {
            return false;
        }

        foreach ($this->urlsets as $urlset) {
            $urlset->save($this->tmpFolder);
            $filenames[] = basename($urlset->getLoc());
        }

        if (!is_null($section)) {
            // Load current SitemapIndex file and add all sitemaps except those,
            // matching section currently being regenerated to root
            foreach ($this->loadCurrentSitemapIndex($targetDir . '/sitemap.xml') as $key => $urlset) {
                // cut possible _X, to compare base section name
                $baseKey = preg_replace('/(.*?)(_\d+)?/', '\1', $key);
                if ($baseKey !== $section) {
                    // we add them to root only, if we add them to $this->urlset
                    // deleteExistingSitemaps() will delete matching files, which we don't want
                    $this->root->addSitemap($urlset);
                }
            }
        }

        file_put_contents($this->tmpFolder . '/sitemap.xml', $this->root->toXml());
        $filenames[] = 'sitemap.xml';

        // if we came to this point - we can activate new files
        // if we fail on exception eariler - old files will stay making Google happy
        $this->activate($targetDir);

        return $filenames;
    }

    /**
     * Prepares temp folder for storing sitemap files
     *
     * @return void
     */
    protected function prepareTempFolder()
    {
        $this->tmpFolder = sys_get_temp_dir() . '/PrestaSitemaps-' . uniqid();
        $this->filesystem->mkdir($this->tmpFolder);
    }

    /**
     * Cleans up temporary files
     *
     * @return void
     */
    protected function cleanup()
    {
        $this->filesystem->remove($this->tmpFolder);
        $this->root = null;
        $this->urlsets = array();
    }

    /**
     * Loads sitemap index XML file and returns array of Urlset objects
     *
     * @param $filename
     *
     * @return array
     * @throws \InvalidArgumentException
     */
    protected function loadCurrentSitemapIndex($filename)
    {
        if (!file_exists($filename)) {
            return array();
        }

        $urlsets = array();
        $index = simplexml_load_file($filename);
        foreach ($index->children() as $child) {
            if ($child->getName() == 'sitemap') {
                if (!isset($child->loc)) {
                    throw new \InvalidArgumentException(
                        "One of referenced sitemaps in $filename doesn't contain 'loc' attribute"
                    );
                }
                $basename = substr(basename($child->loc), 0, -4); // cut .xml

                if (!isset($child->lastmod)) {
                    throw new \InvalidArgumentException(
                        "One of referenced sitemaps in $filename doesn't contain 'lastmod' attribute"
                    );
                }
                $lastmod = new \DateTime($child->lastmod);
                $urlsets[$basename] = $this->newUrlset($basename, $lastmod);
            }
        }

        return $urlsets;
    }

    /**
     * Moves sitemaps created in a temporary folder to their real location
     *
     * @param string $targetDir Directory to move created sitemaps to
     *
     * @throws \RuntimeException
     */
    protected function activate($targetDir)
    {
        if (!is_writable($targetDir)) {
            $this->cleanup();
            throw new \RuntimeException("Can't move sitemaps to $targetDir - directory is not writeable");
        }
        $this->deleteExistingSitemaps($targetDir);

        // no need to delete the root file as it always exists, it will be overwritten
        $this->filesystem->mirror($this->tmpFolder, $targetDir, null, array('override' => true));
        $this->cleanup();
    }

    /**
     * Deletes sitemap files matching filename patterns of newly generated files
     *
     * @param $targetDir string
     */
    protected function deleteExistingSitemaps($targetDir)
    {
        foreach ($this->urlsets as $urlset) {
            $basename = basename($urlset->getLoc());
            if (preg_match('/(.*)_[\d]+\.xml/', $basename)) {
                continue; // skip numbered files
            }
            // pattern is base name of sitemap file (with .xml cut) optionally followed by _X for numbered files
            $pattern = '/' . preg_quote(substr($basename, 0, -4), '/') . '(_\d+)?\.xml/';
            foreach (Finder::create()->in($targetDir)->name($pattern)->files() as $file) {
                $this->filesystem->remove($file);
            }
        }
    }

    /**
     * Factory method for creating Urlset objects
     *
     * @param string $name
     *
     * @param \DateTime $lastmod
     *
     * @return \Presta\SitemapBundle\Sitemap\DumpingUrlset
     */
    protected function newUrlset($name, \DateTime $lastmod = null)
    {
        return new \Presta\SitemapBundle\Sitemap\DumpingUrlset($this->baseUrl . 'sitemap.' . $name . '.xml', $lastmod);
    }
}
