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
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use Symfony\Component\Routing\RouteCollectionBuilder;

if (BaseKernel::VERSION_ID >= 50400) {
    class Kernel extends BaseKernel
    {
        use MicroKernelTrait;

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

        private function configureContainer(
            ContainerConfigurator $container,
            LoaderInterface $loader,
            ContainerBuilder $builder
        ): void {
            $version = sprintf('%s.%s', BaseKernel::MAJOR_VERSION, BaseKernel::MINOR_VERSION);
            $container->import('../config/' . $version . '/*.yaml');
            $container->import('../config/services.yaml');
            if (\PHP_VERSION_ID < 80000) {
                $container->import('../config/' . $version . '/special/annotations.yaml');
            }
        }

        private function configureRoutes(RoutingConfigurator $routes): void
        {
            $version = sprintf('%s.%s', BaseKernel::MAJOR_VERSION, BaseKernel::MINOR_VERSION);
            $routes->import('../config/' . $version . '/{routes}/*.{xml,yaml}');
        }

        public function registerBundles(): iterable
        {
            yield new \Symfony\Bundle\FrameworkBundle\FrameworkBundle();
            yield new \Presta\SitemapBundle\PrestaSitemapBundle();
            if (\PHP_VERSION_ID < 80000) {
                yield new \Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle();
            }
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
} else {
    class Kernel extends BaseKernel
    {
        use MicroKernelTrait;

        protected function configureRoutes(RouteCollectionBuilder $routes): void
        {
            $confDir = $this->getProjectDir() . '/config';
            $version = sprintf('%s.%s', BaseKernel::MAJOR_VERSION, BaseKernel::MINOR_VERSION);
            $routes->import($confDir . '/' . $version . '/{routes}/*.{xml,yaml}', '/', 'glob');
        }

        protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader): void
        {
            $confDir = $this->getProjectDir() . '/config';
            $version = sprintf('%s.%s', BaseKernel::MAJOR_VERSION, BaseKernel::MINOR_VERSION);
            $loader->load($confDir . '/' . $version . '/*.yaml', 'glob');
            $loader->load($confDir . '/services.yaml');
        }

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

        public function registerBundles(): iterable
        {
            yield new \Symfony\Bundle\FrameworkBundle\FrameworkBundle();
            yield new \Presta\SitemapBundle\PrestaSitemapBundle();
            if (\PHP_VERSION_ID < 80000) {
                yield new \Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle();
            }
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
}
