<?php

namespace Presta\SitemapBundle\Tests\Command;

use Presta\SitemapBundle\Command\DumpSitemapsCommand;
use Presta\SitemapBundle\Event\SitemapPopulateEvent;
use Presta\SitemapBundle\Sitemap\Url\GoogleVideoUrlDecorator;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\RouterInterface;

class DumpSitemapsCommandTest extends WebTestCase
{
    /**
     * @var ContainerInterface
     */
    private $container;

    private $fixturesDir;

    private $webDir;

    protected function setUp()
    {
        $this->fixturesDir = realpath(__DIR__ . '/../fixtures');
        $this->webDir = realpath(__DIR__ . '/../web');

        self::createClient();
        $this->container = self::$kernel->getContainer();
        $router = $this->container->get('router');
        /* @var $router RouterInterface */
        $this->container->get('event_dispatcher')
            ->addListener(SitemapPopulateEvent::onSitemapPopulate, function(SitemapPopulateEvent $event) use ($router) {
                $base_url   = $router->generate('PrestaDemoBundle_homepage', array(), true);
                $urlVideo = new GoogleVideoUrlDecorator(
                    new UrlConcrete($base_url . 'page_video1/'),
                    $base_url . 'page_video1/thumbnail_loc?a=b&b=c',
                    'Title & spécial chars',
                    'The description & spécial chars',
                    array('content_loc' => $base_url . 'page_video1/content?format=mov&a=b')
                );

                $urlVideo
                    ->setGalleryLoc($base_url . 'page_video1/gallery_loc/?p=1&sort=desc')
                    ->setGalleryLocTitle('Gallery title & spécial chars');

                $event->getGenerator()->addUrl($urlVideo, 'video');
            });
    }

    protected function tearDown()
    {
        parent::tearDown();
        foreach (glob($this->webDir . '/*{.xml,.xml.gz}', GLOB_BRACE) as $file) {
            unlink($file);
        }
    }

    public function testSitemapDumpWithFullyQualifiedBaseUrl()
    {
        $res = $this->executeDumpWithOptions(array('target' => $this->webDir, '--base-url' => 'http://sitemap.php54.local/'));
        $this->assertEquals(0, $res, 'Command exited with error');
        $this->assertXmlFileEqualsXmlFile($this->fixturesDir . '/sitemap.video.xml', $this->webDir . '/sitemap.video.xml');
    }

    public function testSitemapDumpWithGzip()
    {
        $res = $this->executeDumpWithOptions(array('target' => $this->webDir, '--base-url' => 'http://sitemap.php54.local/', '--gzip' => true));
        $this->assertEquals(0, $res, 'Command exited with error');

        $xml = gzinflate(substr(file_get_contents($this->webDir . '/sitemap.video.xml.gz'), 10, -8));
        $this->assertXmlStringEqualsXmlFile($this->fixturesDir . '/sitemap.video.xml', $xml);

        $expectedSitemaps = array('http://sitemap.php54.local/sitemap.video.xml.gz');
        $this->assertSitemapIndexEquals($this->webDir . '/sitemap.xml', $expectedSitemaps);
    }

    public function testSitemapDumpUpdateExistingIndex()
    {
        copy($this->fixturesDir . '/sitemap.xml', $this->webDir . '/sitemap.xml');

        $this->executeDumpWithOptions(
            array(
                'target' => $this->webDir,
                '--base-url' => 'http://sitemap.php54.local/',
                '--section' => 'video',
                '--gzip' => true
            )
        );

        $expectedSitemaps = array(
            'http://sitemap.php54.local/sitemap.audio.xml',
            'http://sitemap.php54.local/sitemap.video.xml.gz'
        );

        $this->assertSitemapIndexEquals($this->webDir . '/sitemap.xml', $expectedSitemaps);
    }

    public function testSitemapDumpWithInvalidUrl()
    {
        $this->setExpectedException('\InvalidArgumentException', '', DumpSitemapsCommand::ERR_INVALID_HOST);
        $this->executeDumpWithOptions(array('target' => $this->webDir, '--base-url' => 'fake host'));
    }

    private function assertSitemapIndexEquals($sitemapFile, array $expectedSitemaps)
    {
        $xml = simplexml_load_file($sitemapFile);
        $sitemaps = array();
        foreach ($xml->sitemap as $sitemap) {
            $sitemaps[] = (string)$sitemap->loc;
        }
        sort($expectedSitemaps);
        sort($sitemaps);
        $this->assertEquals($expectedSitemaps, $sitemaps);
    }

    private function executeDumpWithOptions(array $input = array())
    {
        $application = new Application(self::$kernel);
        $application->add(new DumpSitemapsCommand());

        $command = $application->find('presta:sitemaps:dump');
        $commandTester = new CommandTester($command);
        $input = array_merge(array('command' => $command->getName()), $input);

        return $commandTester->execute($input);
    }
}
