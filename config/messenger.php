<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Presta\SitemapBundle\Messenger\DumpSitemapMessage;
use Presta\SitemapBundle\Messenger\DumpSitemapMessageHandler;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $services->set('presta_sitemap.messenger.message_handler', DumpSitemapMessageHandler::class)
        ->args([
            service('router'),
            service('presta_sitemap.dumper'),
            '%presta_sitemap.dump_directory%',
        ])
        ->tag('messenger.message_handler', ['handles' => DumpSitemapMessage::class]);
};
