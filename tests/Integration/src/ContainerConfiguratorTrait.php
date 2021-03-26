<?php

namespace Presta\SitemapBundle\Tests\Integration;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Messenger\MessageBusInterface;

if (Kernel::VERSION_ID >= 50100) {
    trait ContainerConfiguratorTrait
    {
        protected function configureContainer(ContainerConfigurator $container): void
        {
            $confDir = $this->getProjectDir() . '/config';

            $container->import($confDir . '/{packages}/*' . self::CONFIG_EXTS);
            $container->import($confDir . '/{packages}/' . $this->environment . '/*' . self::CONFIG_EXTS);
            $container->import($confDir . '/{services}' . self::CONFIG_EXTS);
            $container->import($confDir . '/{services}_' . $this->environment . self::CONFIG_EXTS);
            $container->import($confDir . '/routing.yaml');

            if (interface_exists(MessageBusInterface::class)) {
                $container->import($confDir . '/messenger.yaml');
            }

            $container->import($confDir . '/{packages}/5.1/*' . self::CONFIG_EXTS);
        }
    }
} else {
    trait ContainerConfiguratorTrait
    {
        protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader)
        {
            $confDir = $this->getProjectDir() . '/config';

            $loader->load($confDir . '/{packages}/*' . self::CONFIG_EXTS, 'glob');
            $loader->load($confDir . '/{packages}/' . $this->environment . '/*' . self::CONFIG_EXTS, 'glob');
            $loader->load($confDir . '/{services}' . self::CONFIG_EXTS, 'glob');
            $loader->load($confDir . '/{services}_' . $this->environment . self::CONFIG_EXTS, 'glob');

            if (self::VERSION_ID >= 40200) {
                $loader->load($confDir . '/routing.yaml');
            }

            if (interface_exists(MessageBusInterface::class)) {
                $loader->load($confDir . '/messenger.yaml');
            }
        }
    }
}
