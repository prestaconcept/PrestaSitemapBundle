<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Presta\SitemapBundle\Command\DumpSitemapsCommand;
use Presta\SitemapBundle\Controller\SitemapController;
use Presta\SitemapBundle\Service\Dumper;
use Presta\SitemapBundle\Service\DumperInterface;
use Presta\SitemapBundle\Service\Generator;
use Presta\SitemapBundle\Service\GeneratorInterface;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();
    $parameters->set('presta_sitemap.generator.class', Generator::class);
    $parameters->set('presta_sitemap.dumper.class', Dumper::class);

    $services->set('presta_sitemap.generator_default', '%presta_sitemap.generator.class%')
        ->args([
            service('event_dispatcher'),
            service('router'),
            '%presta_sitemap.items_by_set%',
        ])
        ->call('setDefaults', ['%presta_sitemap.defaults%']);

    $services->set('presta_sitemap.dumper_default', '%presta_sitemap.dumper.class%')
        ->args([
            service('event_dispatcher'),
            service('filesystem'),
            service('router'),
            '%presta_sitemap.sitemap_file_prefix%',
            '%presta_sitemap.items_by_set%',
        ])
        ->call('setDefaults', ['%presta_sitemap.defaults%']);

    $services->set('presta_sitemap.dump_command', DumpSitemapsCommand::class)
        ->public()
        ->autoconfigure()
        ->args([
            service('router'),
            service('presta_sitemap.dumper'),
            '%presta_sitemap.dump_directory%',
        ])
        ->tag('console.command');

    $services->alias(GeneratorInterface::class, 'presta_sitemap.generator');

    $services->alias(DumperInterface::class, 'presta_sitemap.dumper');

    $services->alias(SitemapController::class, 'presta_sitemap.controller')
        ->public();

    $services->set('presta_sitemap.controller', SitemapController::class)
        ->public()
        ->args([
            service('presta_sitemap.generator'),
            '%presta_sitemap.timetolive%',
        ]);
};
