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

if (Kernel::VERSION_ID >= 50100) {
    trait RouteConfiguratorTrait
    {
        protected function configureRoutes(RoutingConfigurator $routes)
        {
            $confDir = $this->getProjectDir() . '/config';

            $routes->import($confDir . '/{routes}/' . $this->environment . '/*' . self::CONFIG_EXTS);
            $routes->import($confDir . '/{routes}/*' . self::CONFIG_EXTS);
            $routes->import($confDir . '/{routes}' . self::CONFIG_EXTS);

            $routes->import($confDir . '/{routes}/5.1/*' . self::CONFIG_EXTS);
        }
    }
} else {
    trait RouteConfiguratorTrait
    {
        protected function configureRoutes(RouteCollectionBuilder $routes)
        {
            $confDir = $this->getProjectDir() . '/config';

            $routes->import($confDir . '/{routes}/' . $this->environment . '/*' . self::CONFIG_EXTS, '/', 'glob');
            $routes->import($confDir . '/{routes}/*' . self::CONFIG_EXTS, '/', 'glob');
            $routes->import($confDir . '/{routes}' . self::CONFIG_EXTS, '/', 'glob');
        }
    }
}
