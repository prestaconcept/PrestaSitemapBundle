<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Presta\SitemapBundle\Event\SitemapAddUrlEvent;
use Presta\SitemapBundle\EventListener\StaticRoutesAlternateEventListener;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('presta_sitemap.event_listener.static_routes_alternate', StaticRoutesAlternateEventListener::class)
        ->args([
            service('router'),
            '%presta_sitemap.alternate%',
        ])
        ->tag('kernel.event_listener', ['event' => SitemapAddUrlEvent::class, 'method' => 'addAlternate']);
};
