<?php

/**
 * This file is part of the PrestaSitemapBundle package.
 *
 * (c) PrestaConcept <www.prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle\Messenger;

/**
 * Message to dump the sitemaps asynchronously or synchronously in background
 *
 * @author Tomas Norkūnas <norkunas.tom@gmail.com>
 */
class DumpSitemapMessage
{
    /**
     * @var string|null
     */
    private $section;

    /**
     * @var string|null
     */
    private $baseUrl;

    /**
     * @var string|null
     */
    private $targetDir;

    /**
     * @var array
     */
    private $options;

    public function __construct(string $section = null, string $baseUrl = null, string $targetDir = null, array $options = [])
    {
        $this->section = $section;
        $this->baseUrl = $baseUrl;
        $this->targetDir = $targetDir;
        $this->options = $options;
    }

    public function getSection(): ?string
    {
        return $this->section;
    }

    public function getBaseUrl(): ?string
    {
        return $this->baseUrl;
    }

    public function getTargetDir(): ?string
    {
        return $this->targetDir;
    }

    public function getOptions(): array
    {
        return $this->options;
    }
}
