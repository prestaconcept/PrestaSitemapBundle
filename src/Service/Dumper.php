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

use Presta\SitemapBundle\DependencyInjection\Configuration;
use Presta\SitemapBundle\Sitemap\DumpingUrlset;
use Presta\SitemapBundle\Sitemap\Urlset;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

/**
 * Service for dumping sitemaps into static files
 *
 * @author Konstantin Tjuterev <kostik.lv@gmail.com>
 * @author Konstantin Myakshin <koc-dp@yandex.ru>
 */
class Dumper extends AbstractGenerator implements DumperInterface
{
    /**
     * Path to folder where temporary files will be created
     * @var string
     */
    protected $tmpFolder;

    /**
     * Base URL where dumped sitemap files can be accessed (we can't guess that from console)
     * @var string
     */
    protected $baseUrl;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var string
     */
    protected $sitemapFilePrefix;

    /**
     * @param EventDispatcherInterface $dispatcher Symfony's EventDispatcher
     * @param Filesystem               $filesystem Symfony's Filesystem
     * @param string                   $sitemapFilePrefix
     * @param int|null                 $itemsBySet
     */
    public function __construct(
        EventDispatcherInterface $dispatcher,
        Filesystem $filesystem,
        $sitemapFilePrefix = Configuration::DEFAULT_FILENAME,
        $itemsBySet = null
    ) {
        parent::__construct($dispatcher, $itemsBySet);

        $this->filesystem = $filesystem;
        $this->sitemapFilePrefix = $sitemapFilePrefix;
    }

    /**
     * @inheritdoc
     */
    public function dump($targetDir, $host, $section = null, array $options = [])
    {
        $options = array_merge(['gzip' => false], $options);

        $this->baseUrl = rtrim($host, '/') . '/';
        // we should prepare temp folder each time, because dump may be called several times (with different sections)
        // and activate command below removes temp folder
        $this->prepareTempFolder();

        $filenames = [];

        try {
            $this->populate($section);

            // if no urlset wasn't created during populating
            // it means no URLs were added to the sitemap
            if (!count($this->urlsets)) {
                $this->cleanup();

                return false;
            }

            foreach ($this->urlsets as $urlset) {
                if ($urlset instanceof DumpingUrlset) {
                    $urlset->save($this->tmpFolder, $options['gzip']);
                }
                $filenames[] = basename($urlset->getLoc());
            }

            if (null !== $section) {
                // Load current SitemapIndex file and add all sitemaps except those,
                // matching section currently being regenerated to root
                $index = $this->loadCurrentSitemapIndex($targetDir . '/' . $this->sitemapFilePrefix . '.xml');
                foreach ($index as $key => $urlset) {
                    // cut possible _X, to compare base section name
                    $baseKey = preg_replace('/(.*?)(_\d+)?/', '\1', $key);
                    if ($baseKey !== $section) {
                        // we add them to root only, if we add them to $this->urlset
                        // deleteExistingSitemaps() will delete matching files, which we don't want
                        $this->getRoot()->addSitemap($urlset);
                    }
                }
            }

            file_put_contents($this->tmpFolder . '/' . $this->sitemapFilePrefix . '.xml', $this->getRoot()->toXml());
            $filenames[] = $this->sitemapFilePrefix . '.xml';

            // if we came to this point - we can activate new files
            // if we fail on exception eariler - old files will stay making Google happy
            $this->activate($targetDir);
        } finally {
            $this->cleanup();
        }

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
        $this->urlsets = [];
    }

    /**
     * Loads sitemap index XML file and returns array of Urlset objects
     *
     * @param string $filename
     *
     * @return Urlset[]
     * @throws \InvalidArgumentException
     */
    protected function loadCurrentSitemapIndex($filename)
    {
        if (!file_exists($filename)) {
            return [];
        }

        $urlsets = [];
        $index = simplexml_load_file($filename);
        foreach ($index->children() as $child) {
            /** @var $child \SimpleXMLElement */
            if ($child->getName() === 'sitemap') {
                if (!isset($child->loc)) {
                    throw new \InvalidArgumentException(
                        "One of referenced sitemaps in $filename doesn't contain 'loc' attribute"
                    );
                }
                preg_match(
                    '/^' . preg_quote($this->sitemapFilePrefix) . '\.(.+)\.xml(\.gz)?$/',
                    basename($child->loc),
                    $matches
                ); // cut .xml|.xml.gz and check gz files
                $basename = $matches[1];
                $gzOption = isset($matches[2]);

                if (!isset($child->lastmod)) {
                    throw new \InvalidArgumentException(
                        "One of referenced sitemaps in $filename doesn't contain 'lastmod' attribute"
                    );
                }
                $lastmod = new \DateTimeImmutable($child->lastmod);
                $urlsets[$basename] = $this->newUrlset($basename, $lastmod, $gzOption);
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
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        if (!is_writable($targetDir)) {
            throw new \RuntimeException(
                sprintf('Can\'t move sitemaps to "%s" - directory is not writeable', $targetDir)
            );
        }

        $this->deleteExistingSitemaps($targetDir);

        // no need to delete the root file as it always exists, it will be overwritten
        $this->filesystem->mirror($this->tmpFolder, $targetDir, null, ['override' => true]);
    }

    /**
     * Deletes sitemap files matching filename patterns of newly generated files
     *
     * @param string $targetDir
     */
    protected function deleteExistingSitemaps($targetDir)
    {
        foreach ($this->urlsets as $urlset) {
            if (preg_match('/.*_\d+\.xml(\.gz)?$/', $urlset->getLoc())) {
                continue; // skip numbered files
            }

            // pattern is base name of sitemap file (with .xml cut) optionally followed by _X for numbered files
            $basename = basename($urlset->getLoc());
            $basename = preg_replace('/\.xml(\.gz)?$/', '', $basename); // cut .xml|.xml.gz
            $pattern = '/' . preg_quote($basename, '/') . '(_\d+)?\.xml(\.gz)?$/';

            foreach (Finder::create()->in($targetDir)->depth(0)->name($pattern)->files() as $file) {
                // old sitemap files are removed only if not existing in new file set
                if (!$this->filesystem->exists($this->tmpFolder . '/' . $file->getFilename())) {
                    $this->filesystem->remove($file);
                }
            }
        }
    }

    /**
     * Create new DumpingUrlset with gz option
     *
     * @param string $name The urlset name
     * @param \DateTimeInterface|null $lastmod The urlset last modification date
     * @param bool $gzExtension Whether the urlset is gzipped
     * @return DumpingUrlset|Urlset
     */
    protected function newUrlset($name, \DateTimeInterface $lastmod = null, bool $gzExtension = false)
    {
        $url = $this->baseUrl . $this->sitemapFilePrefix . '.' . $name . '.xml';
        if ($gzExtension) {
            $url .= '.gz';
        }

        return new DumpingUrlset($url, $lastmod);
    }
}
