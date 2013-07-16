<?php
/**
 * User: avasilenko
 * Date: 17.7.13
 * Time: 00:00
 */
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
    
    protected function setUp()
    {
        self::createClient();
        $this->container = self::$kernel->getContainer();
        /** @var RouterInterface $router */
        $router = $this->container->get('router');
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

    public function testSitemapDumpWithFullyQualifiedBaseUrl()
    {
        $res = $this->executeDumpWithOptions(array('target' => __DIR__ . '/../web', '--base-url' => 'http://sitemap.php54.local/'));
        $this->assertEquals(0, $res, 'Command exited with error');
        $this->assertXmlFileEqualsXmlFile(__DIR__ . '/../sitemap.video.xml', __DIR__ . '/../web/sitemap.video.xml');
    }
    
    public function testSitemapDumpWithInvalidUrl()
    {
        $this->setExpectedException('\InvalidArgumentException', '', DumpSitemapsCommand::ERR_INVALID_HOST);
        $this->executeDumpWithOptions(array('target' => __DIR__ . '/../web', '--base-url' => 'fake host'));
    }
    
    private function executeDumpWithOptions(array $options = array()) 
    {
        $application = new Application(self::$kernel);
        $application->add(new DumpSitemapsCommand());

        $command = $application->find('presta:sitemaps:dump');
        $commandTester = new CommandTester($command);
        $options = array_merge(array('command' => $command->getName()), $options);
        
        return $commandTester->execute($options);   
    }
}
