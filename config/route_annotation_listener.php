<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Presta\SitemapBundle\Event\SitemapPopulateEvent;
use Presta\SitemapBundle\EventListener\RouteAnnotationEventListener;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();
    $parameters->set('presta_sitemap.eventlistener.route_annotation.class', RouteAnnotationEventListener::class);

    $services->set('presta_sitemap.eventlistener.route_annotation', '%presta_sitemap.eventlistener.route_annotation.class%')
        ->args([
            service('router'),
            service('event_dispatcher'),
            '%presta_sitemap.default_section%',
        ])
        ->tag('kernel.event_listener', ['event' => SitemapPopulateEvent::class, 'method' => 'registerRouteAnnotation']);
};
