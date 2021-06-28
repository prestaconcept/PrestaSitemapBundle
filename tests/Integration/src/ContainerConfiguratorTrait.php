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

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel;

if (Kernel::VERSION_ID >= 50300) {
    trait ContainerConfiguratorTrait
    {
        protected function configureContainer(ContainerConfigurator $container): void
        {
            $container->import('../config/{packages}/*.yaml');
            $container->import('../config/services.yaml');
            $container->import('../config/messenger.yaml');
            $container->import('../config/{packages}/5.x/presta_sitemap.yaml');
            $container->import('../config/{packages}/5.3/framework.yaml');
            if (\PHP_VERSION_ID < 80000) {
                $container->import('../config/{packages}/5.3/annotations.yaml');
            }
        }
    }
} elseif (Kernel::VERSION_ID >= 50100) {
    trait ContainerConfiguratorTrait
    {
        protected function configureContainer(ContainerConfigurator $container): void
        {
            $confDir = $this->getProjectDir() . '/config';
            $container->import($confDir . '/{packages}/*.yaml');
            $container->import($confDir . '/{services}.yaml');
            $container->import($confDir . '/messenger.yaml');
            $container->import($confDir . '/{packages}/5.x/presta_sitemap.yaml');
        }
    }
} else {
    trait ContainerConfiguratorTrait
    {
        protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader)
        {
            $confDir = $this->getProjectDir() . '/config';
            $loader->load($confDir . '/{packages}/*.yaml', 'glob');
            $loader->load($confDir . '/{services}.yaml', 'glob');
            $loader->load($confDir . '/messenger.yaml');
        }
    }
}
