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

    public function testAlternate()
    {
        $containerBuilder = new ContainerBuilder();

        $configs = [
            'presta_sitemap' => [
                'alternate' => [
                    'default_locale' => 'en',
                    'locales' => ['en', 'it'],
                    'i18n' => 'jms',
                ],
            ],
        ];

        $extension = new PrestaSitemapExtension();
        $extension->load($configs, $containerBuilder);

        self::assertTrue($containerBuilder->hasParameter('presta_sitemap.alternate'));

        $alternateArray = $containerBuilder->getParameter('presta_sitemap.alternate');

        self::assertIsArray($alternateArray);
        self::assertTrue($alternateArray['enabled']);
        self::assertArrayHasKey('default_locale', $alternateArray);
        self::assertEquals('en', $alternateArray['default_locale']);
    }
}
