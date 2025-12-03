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

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public function __construct(string $environment, bool $debug)
    {
        $this->setupRouteAlias();

        parent::__construct($environment, $debug);
    }

    // TODO: Remove after dropping support for Symfony 7.x
    private function setupRouteAlias(): void
    {
        if (class_exists('Symfony\Component\Routing\Annotation\Route')) {
            class_alias('Symfony\Component\Routing\Annotation\Route', 'Presta\SitemapBundle\Route');
        } elseif (class_exists('Symfony\Component\Routing\Attribute\Route')) {
            class_alias('Symfony\Component\Routing\Attribute\Route', 'Presta\SitemapBundle\Route');
        }
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

    private function configureContainer(
        ContainerConfigurator $container,
        LoaderInterface $loader,
        ContainerBuilder $builder
    ): void {
        $version = sprintf('%s.%s', BaseKernel::MAJOR_VERSION, BaseKernel::MINOR_VERSION);
        $container->import('../config/' . $version . '/*.yaml');
        $container->import('../config/services.yaml');
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
    }

    public function boot(): void
    {
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
