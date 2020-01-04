<?php

namespace Presta\SitemapBundle\Test\Sitemap;

use Presta\SitemapBundle\Service\Dumper;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @author Davide Dell'Erba <info@davidedellerba.it>
 */
class SitemapZgipOptionTest extends WebTestCase
{
    /**
     * @var ContainerInterface
     */
    protected static $container;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /** @var Filesystem */
    private $filesystem;

    public function setUp()
    {
        self::createClient(['debug' => false]);
        if (self::$container === null) {
            self::$container = self::$kernel->getContainer();
        }
        $this->eventDispatcher = self::$container->get('event_dispatcher');
        $this->filesystem = self::$container->get('filesystem');
    }

    public function testSitemapWithoutGzip()
    {
        $this->setUp();
        $dumper = new Dumper($this->eventDispatcher, $this->filesystem);
        $method = new \ReflectionMethod($dumper, 'loadCurrentSitemapIndex');
        $method->setAccessible(true);
        $data = $method->invoke($dumper, realpath(__DIR__ . '/../fixtures') . '/sitemap_without_gz.xml');
        self::assertNotRegExp('/\.gz$/i', $data['static']->getLoc());
        self::assertNotRegExp('/\.gz$/i', $data['dynamic']->getLoc());
    }

    public function testSitemapWithGzip()
    {
        $this->setUp();
        $dumper = new Dumper($this->eventDispatcher, $this->filesystem);
        $method = new \ReflectionMethod($dumper, 'loadCurrentSitemapIndex');
        $method->setAccessible(true);
        $data = $method->invoke($dumper, realpath(__DIR__ . '/../fixtures') . '/sitemap_with_gz.xml');
        self::assertNotRegExp('/\.gz$/i', $data['static']->getLoc());
        self::assertRegExp('/\.gz$/i', $data['dynamic']->getLoc());
    }
}