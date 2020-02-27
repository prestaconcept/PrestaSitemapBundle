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
use Presta\SitemapBundle\Service\Dumper;
use Presta\SitemapBundle\Sitemap\Url\GoogleVideo;
use Presta\SitemapBundle\Sitemap\Url\GoogleVideoUrlDecorator;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
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
    protected static $container;

    private $fixturesDir;

    private $webDir;

    protected function setUp()
    {
        $this->fixturesDir = realpath(__DIR__ . '/../fixtures');
        $this->webDir = realpath(__DIR__ . '/../web');

        self::createClient(['debug' => false]);
        if (self::$container === null) {
            self::$container = self::$kernel->getContainer();
        }

        $router = self::$container->get('router');
        /* @var $router RouterInterface */

        $router->getContext()->fromRequest(Request::create('http://sitemap.php54.local'));

        self::$container->get('event_dispatcher')
            ->addListener(
                SitemapPopulateEvent::ON_SITEMAP_POPULATE,
                function (SitemapPopulateEvent $event) use ($router) {
                    $base_url   = $router->generate('PrestaDemoBundle_homepage', [], UrlGeneratorInterface::ABSOLUTE_URL);
                    $video = new GoogleVideo(
                        $base_url . 'page_video1/thumbnail_loc?a=b&b=c',
                        'Title & spécial chars',
                        'The description & spécial chars',
                        ['content_loc' => $base_url.'page_video1/content?format=mov&a=b']
                    );
                    $video
                        ->setGalleryLoc($base_url . 'page_video1/gallery_loc/?p=1&sort=desc')
                        ->setGalleryLocTitle('Gallery title & spécial chars');

                    $urlVideo = new GoogleVideoUrlDecorator(new UrlConcrete($base_url . 'page_video1/'));
                    $urlVideo->addVideo($video);

                    $event->getUrlContainer()->addUrl($urlVideo, 'video');
                }
            );
    }

    protected function tearDown() : void
    {
        parent::tearDown();
        self::$container = null;
        foreach (glob($this->webDir . '/*{.xml,.xml.gz}', GLOB_BRACE) as $file) {
            unlink($file);
        }
    }

    public function testSitemapDumpWithGzip()
    {
        $res = $this->executeDumpWithOptions(['target' => $this->webDir, '--gzip' => true]);
        self::assertEquals(0, $res, 'Command exited with error');

        $xml = gzinflate(substr(file_get_contents($this->webDir . '/sitemap.video.xml.gz'), 10, -8));
        self::assertXmlStringEqualsXmlFile($this->fixturesDir . '/sitemap.video.xml', $xml);

        $expectedSitemaps = ['http://sitemap.php54.local/sitemap.video.xml.gz'];
        $this->assertSitemapIndexEquals($this->webDir . '/sitemap.xml', $expectedSitemaps);
    }

    public function testSitemapDumpUpdateExistingIndex()
    {
        copy($this->fixturesDir . '/sitemap.xml', $this->webDir . '/sitemap.xml');

        $this->executeDumpWithOptions(
            [
                'target' => $this->webDir,
                '--section' => 'video',
                '--gzip' => true
            ]
        );

        $expectedSitemaps = [
            'http://sitemap.php54.local/sitemap.audio.xml',
            'http://sitemap.php54.local/sitemap.video.xml.gz',
        ];

        $this->assertSitemapIndexEquals($this->webDir . '/sitemap.xml', $expectedSitemaps);
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
        self::assertEquals($expectedSitemaps, $sitemaps);
    }

    private function executeDumpWithOptions(array $input = [])
    {
        $application = new Application(self::$kernel);
        $application->add(
            new DumpSitemapsCommand(
                self::$container->get('router'),
                new Dumper(self::$container->get('event_dispatcher'), self::$container->get('filesystem')),
                'public'
            )
        );

        $command = $application->find('presta:sitemaps:dump');
        $commandTester = new CommandTester($command);
        $input = array_merge(['command' => $command->getName()], $input);

        return $commandTester->execute($input);
    }
}
