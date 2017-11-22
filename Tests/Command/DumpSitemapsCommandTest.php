<?php

/**
 * This file is part of the PrestaSitemapBundle package.
 *
 * (c) PrestaConcept <www.prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle\Tests\Command;

use Presta\SitemapBundle\Command\DumpSitemapsCommand;
use Presta\SitemapBundle\Event\SitemapPopulateEvent;
use Presta\SitemapBundle\Sitemap\Url\GoogleVideoUrlDecorator;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * @author Alex Vasilenko
 */
class DumpSitemapsCommandTest extends WebTestCase
{
    /**
     * @var ContainerInterface
     */
    private $container;

    private static $fixturesDir;

    private static $webDir;

    public static function setUpBeforeClass()
    {
        self::$fixturesDir = realpath(__DIR__ . '/../fixtures');
        self::$webDir = realpath(__DIR__ . '/../web');
    }

    protected function setUp()
    {
        self::bootKernel(['debug' => false]);
        $this->container = self::$kernel->getContainer();
        $router = $this->container->get('router');
        /* @var $router RouterInterface */

        $router->getContext()->fromRequest(Request::create('http://sitemap.php54.local'));

        $this->container->get('event_dispatcher')
            ->addListener(
                SitemapPopulateEvent::ON_SITEMAP_POPULATE,
                function (SitemapPopulateEvent $event) use ($router) {
                    $base_url = $router->generate('PrestaDemoBundle_homepage', [], UrlGeneratorInterface::ABSOLUTE_URL);
                    $urlVideo = new GoogleVideoUrlDecorator(
                        new UrlConcrete($base_url . 'page_video1/'),
                        $base_url . 'page_video1/thumbnail_loc?a=b&b=c',
                        'Title & spécial chars',
                        'The description & spécial chars',
                        ['content_loc' => $base_url . 'page_video1/content?format=mov&a=b']
                    );

                    $urlVideo
                        ->setGalleryLoc($base_url . 'page_video1/gallery_loc/?p=1&sort=desc')
                        ->setGalleryLocTitle('Gallery title & spécial chars');

                    $event->getUrlContainer()->addUrl($urlVideo, 'video');
                }
            );
    }

    protected function tearDown()
    {
        parent::tearDown();
        foreach (glob(self::$webDir . '/*{.xml,.xml.gz}', GLOB_BRACE) as $file) {
            unlink($file);
        }
    }

    public function testSitemapDumpWithGzip()
    {
        $res = $this->executeDumpWithOptions(['target' => self::$webDir, '--gzip' => true]);
        $this->assertEquals(0, $res, 'Command exited with error');

        $xml = gzinflate(substr(file_get_contents(self::$webDir . '/sitemap.video.xml.gz'), 10, -8));
        $this->assertXmlStringEqualsXmlFile(self::$fixturesDir . '/sitemap.video.xml', $xml);

        $expectedSitemaps = ['http://sitemap.php54.local/sitemap.video.xml.gz'];
        $this->assertSitemapIndexEquals(self::$webDir . '/sitemap.xml', $expectedSitemaps);
    }

    public function testSitemapDumpUpdateExistingIndex()
    {
        copy(self::$fixturesDir . '/sitemap.xml', self::$webDir . '/sitemap.xml');

        $this->executeDumpWithOptions(
            [
                'target' => self::$webDir,
                '--section' => 'video',
                '--gzip' => true,
            ]
        );

        $expectedSitemaps = [
            'http://sitemap.php54.local/sitemap.audio.xml',
            'http://sitemap.php54.local/sitemap.video.xml.gz',
        ];

        $this->assertSitemapIndexEquals(self::$webDir . '/sitemap.xml', $expectedSitemaps);
    }

    private function assertSitemapIndexEquals($sitemapFile, array $expectedSitemaps)
    {
        $xml = simplexml_load_file($sitemapFile);
        $sitemaps = [];
        foreach ($xml->sitemap as $sitemap) {
            $sitemaps[] = (string)$sitemap->loc;
        }
        sort($expectedSitemaps);
        sort($sitemaps);
        $this->assertEquals($expectedSitemaps, $sitemaps);
    }

    private function executeDumpWithOptions(array $input = [])
    {
        $application = new Application(self::$kernel);
        $application->add($this->container->get('presta_sitemap.dump_command'));

        $command = $application->find('presta:sitemaps:dump');
        $commandTester = new CommandTester($command);
        $input = array_merge(['command' => $command->getName()], $input);

        return $commandTester->execute($input, ['verbosity' => OutputInterface::VERBOSITY_QUIET]);
    }
}
