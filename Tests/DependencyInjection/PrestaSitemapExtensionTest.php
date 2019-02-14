<?php

namespace Presta\SitemapBundle\Test\DependencyInjection;

use Presta\SitemapBundle\DependencyInjection\PrestaSitemapExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class RouteAnnotationEventListenerTest extends \PHPUnit_Framework_TestCase
{
    public function testDumperAliasIsSet()
    {
        $containerBuilder = new ContainerBuilder();

        $extension = new PrestaSitemapExtension();
        $extension->load(array(), $containerBuilder);

        $this->assertTrue($containerBuilder->hasAlias('Presta\SitemapBundle\Service\DumperInterface'));
    }
}
