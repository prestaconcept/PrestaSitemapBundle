<?php

/*
 * This file is part of the PrestaSitemapBundle package.
 *
 * (c) PrestaConcept <https://prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle\Tests\Integration;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use ContainerConfiguratorTrait;
    use MicroKernelTrait;
    use RouteConfiguratorTrait;

    const CONFIG_EXTS = '.{php,xml,yaml,yml}';

    public function getCacheDir(): string
    {
        return $this->getProjectDir() . '/var/cache/' . $this->environment;
    }

    public function getLogDir(): string
    {
        return $this->getProjectDir() . '/var/log';
    }

    public function getProjectDir(): string
    {
        return \dirname(__DIR__);
    }

    public function registerBundles(): array
    {
        return [
            new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new \Presta\SitemapBundle\PrestaSitemapBundle(),
        ];
    }

    public function boot()
    {
        /* force "var" dir to be removed the first time this kernel boot */
        static $cleanVarDirectory = true;

        if ($cleanVarDirectory === true) {
            $varDirectory = $this->getProjectDir() . '/var';
            if (is_dir($varDirectory)) {
                (new Filesystem())->remove($varDirectory);
            }
            $cleanVarDirectory = false;
        }

        parent::boot();
    }
}
