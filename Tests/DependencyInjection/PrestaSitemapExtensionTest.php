<?php

namespace Presta\SitemapBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Presta\SitemapBundle\DependencyInjection\PrestaSitemapExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class PrestaSitemapExtensionTest extends TestCase
{
    public function testDumperAliasIsSet()
    {
        $containerBuilder = new ContainerBuilder();

        $extension = new PrestaSitemapExtension();
        $extension->load([], $containerBuilder);

        self::assertTrue($containerBuilder->hasAlias('Presta\SitemapBundle\Service\DumperInterface'));
    }
}
