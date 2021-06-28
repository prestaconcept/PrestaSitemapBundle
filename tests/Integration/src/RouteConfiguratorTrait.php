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

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use Symfony\Component\Routing\RouteCollectionBuilder;

if (Kernel::VERSION_ID >= 50300) {
    trait RouteConfiguratorTrait
    {
        protected function configureRoutes(RoutingConfigurator $routes): void
        {
            $routes->import('../config/{routes}/*.{xml,yaml}');
            $routes->import('../config/{routes}/5.x/translated.yaml');
        }
    }
} elseif (Kernel::VERSION_ID >= 50100) {
    trait RouteConfiguratorTrait
    {
        protected function configureRoutes(RoutingConfigurator $routes): void
        {
            $confDir = $this->getProjectDir() . '/config';
            $routes->import($confDir . '/{routes}/*.{xml,yaml}');
            $routes->import($confDir . '/{routes}/5.x/translated.yaml');
        }
    }
} else {
    trait RouteConfiguratorTrait
    {
        protected function configureRoutes(RouteCollectionBuilder $routes)
        {
            $confDir = $this->getProjectDir() . '/config';
            $routes->import($confDir . '/{routes}/*.{xml,yaml}', '/', 'glob');
        }
    }
}
