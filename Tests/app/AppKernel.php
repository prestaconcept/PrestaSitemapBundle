<?php

/**
 * This file is part of the PrestaSitemapBundle package.
 *
 * (c) PrestaConcept <www.prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // Dependencies
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Presta\SitemapBundle\PrestaSitemapBundle(),
        );

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        // We dont need that Environment stuff, just one config
        if (version_compare(self::VERSION, '3.4.0-RC1', '>=')) {
            $loader->load(__DIR__.'/config.sf4.yml');
        } else {
            $loader->load(__DIR__.'/config.yml');
        }
    }
}
