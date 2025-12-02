<?php

use Presta\SitemapBundle\Controller\SitemapController;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routes): void {
    $routes->add('PrestaSitemapBundle_index', '/%presta_sitemap.sitemap_file_prefix%.{_format}')
        ->controller([SitemapController::class, 'indexAction'])
        ->requirements(['_format' => 'xml']);

    $routes->add('PrestaSitemapBundle_section', '/%presta_sitemap.sitemap_file_prefix%.{name}.{_format}')
        ->controller([SitemapController::class, 'sectionAction'])
        ->requirements(['_format' => 'xml']);
};

