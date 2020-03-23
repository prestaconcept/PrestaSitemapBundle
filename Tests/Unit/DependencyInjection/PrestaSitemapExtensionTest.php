<?php

namespace Presta\SitemapBundle\Tests\Unit\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Presta\SitemapBundle\DependencyInjection\Configuration;
use Presta\SitemapBundle\DependencyInjection\PrestaSitemapExtension;
use Presta\SitemapBundle\Service\DumperInterface;
use Presta\SitemapBundle\Service\GeneratorInterface;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;
use Presta\SitemapBundle\Sitemap\XmlConstraint;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Routing\Router;

class PrestaSitemapExtensionTest extends TestCase
{
    private const SERVICES = [
        ['presta_sitemap.eventlistener.route_annotation', 'Static routes listener', 'kernel.event_subscriber'],
        ['presta_sitemap.controller', 'Sitemap controller', null],
        ['presta_sitemap.dump_command', 'Dump sitemap command', 'console.command'],
    ];
    private const ALIASES = [
        ['Generator', 'presta_sitemap.generator_default', GeneratorInterface::class, 'presta_sitemap.generator'],
        ['Dumper', 'presta_sitemap.dumper_default', DumperInterface::class, 'presta_sitemap.dumper'],
    ];

    private const TTL = 3600;
    private const PREFIX = Configuration::DEFAULT_FILENAME;
    private const ITEMS_BY_SET = XmlConstraint::LIMIT_ITEMS + 1;
    private const DEFAULTS = ['priority' => 0.5, 'changefreq' => UrlConcrete::CHANGEFREQ_DAILY, 'lastmod' => 'now'];
    private const PARAMETERS = [
        ['presta_sitemap.timetolive', self::TTL, 'Cache lifetime'],
        ['presta_sitemap.sitemap_file_prefix', self::PREFIX, 'Sitemap filename prefix'],
        ['presta_sitemap.items_by_set', self::ITEMS_BY_SET, 'Items count limit by sitemap file'],
        ['presta_sitemap.defaults', self::DEFAULTS, 'Sitemap items default options'],
    ];

    public function testLoadWithoutConfig()
    {
        $container = new ContainerBuilder();
        $extension = new PrestaSitemapExtension();
        $extension->load([], $container);

        // assert that services where registered properly
        foreach (self::SERVICES as [$id, $name, $tag]) {
            self::assertTrue($container->hasDefinition($id),
                sprintf('%s service definition is registered', $name)
            );
            if ($tag !== null) {
                $staticRoutesListenerServiceDefinition = $container->getDefinition($id);
                self::assertTrue($staticRoutesListenerServiceDefinition->hasTag($tag),
                    sprintf('%s service definition is tagged with "%s"', $id, $tag)
                );
            }
        }

        // assert that main services are also registered using their aliases
        foreach (self::ALIASES as [$name, $concreteId, $interface, $alias]) {
            // get concrete service definition
            self::assertTrue($container->hasDefinition($concreteId),
                sprintf('%s service concrete definition is "%s"', $name, $concreteId)
            );
            $concreteDefinition = $container->getDefinition($concreteId);

            // find config aliased definition
            self::assertTrue($container->hasAlias($alias),
                sprintf('%s default alias for "%s" exists', $name, $alias)
            );
            $aliasDefinition = $container->findDefinition($interface);

            // find interface aliased definition
            self::assertTrue($container->hasAlias($interface),
                sprintf('%s interface alias for "%s" exists', $name, $interface)
            );
            $interfaceDefinition = $container->findDefinition($interface);

            // ensure aliased definition references concrete definition
            self::assertTrue($concreteDefinition === $interfaceDefinition && $concreteDefinition === $aliasDefinition,
                sprintf('%s services aliases references the same concrete definition', $name)
            );

            // find concrete definition service class and ensure that service implement the interface
            $classParameter = str_replace('%', '', $concreteDefinition->getClass());
            self::assertTrue($container->hasParameter($classParameter),
                sprintf('%s service class references a parameter named "%s"', $name, $classParameter)
            );
            self::assertTrue(in_array($interface, class_implements($container->getParameter($classParameter))),
                sprintf('%s service definition implements "%s"', $name, $interface)
            );
        }

        // assert that parameters are registered with their default values
        foreach (self::PARAMETERS as [$id, $value, $name]) {
            self::assertTrue($container->hasParameter($id),
                sprintf('Container has value for parameter "%s"', $id)
            );
            self::assertSame($value, $container->getParameter($id),
                sprintf('Container parameter "%s" default value is the one expected', $id)
            );
        }

        // provide some fake (but required) parameters & definitions
        $container->setParameter('kernel.project_dir', __DIR__);
        $container->setDefinition('router', new Definition(Router::class));
        $container->setDefinition('event_dispatcher', new Definition(EventDispatcher::class));
        $container->setDefinition('filesystem', new Definition(Filesystem::class));
        $container->compile();
        self::assertTrue(true, 'Container compiled successfully');
    }
}
