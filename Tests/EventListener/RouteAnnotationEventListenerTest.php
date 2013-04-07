<?php

namespace Presta\SitemapBundle\Test\Sitemap;

use Presta\SitemapBundle\EventListener\RouteAnnotationEventListener;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;

/**
* Manage sitemaps listing
*
* @author  David Epely
*/
class RouteAnnotationEventListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * test no "sitemap" annotation
     */
    public function testNoAnnotation()
    {
        $this->assertEquals(null,$this->getListener()->getOptions('route1', $this->getRoute(null)),'sitemap = null returns null');
    }

    /**
     * test "sitemap"=false annotation
     */
    public function testInvalidSitemapFalse()
    {
        $this->setExpectedException('InvalidArgumentException');
        $this->assertEquals(-1,$this->getListener()->getOptions('route1',$this->getRoute(false)),'sitemap = false throws an exception');
    }

    /**
     * test "sitemap"="anything" annotation
     */
    public function testInvalidSitemapArbitrary()
    {
        $this->setExpectedException('InvalidArgumentException');
        $this->assertEquals(-1,$this->getListener()->getOptions('route1',$this->getRoute('anything')),'sitemap = "anything" throws an exception');
    }

    /**
     * test "sitemap"=true
     */
    public function testDefaultAnnotation()
    {
        $result=$this->getListener()->getOptions('route1',$this->getRoute(true));
        $this->assertArrayHasKey('priority',$result);
        $this->assertArrayHasKey('changefreq',$result);
        $this->assertArrayHasKey('lastmod',$result);
        $this->assertEquals(1,$result['priority']);
        $this->assertEquals(UrlConcrete::CHANGEFREQ_DAILY,$result['changefreq']);
        $this->assertInstanceOf('\DateTime',$result['lastmod']);
    }

    /**
     * test "sitemap = {"priority" = "0.5"}
     */
    public function testValidPriority()
    {
        $result=$this->getListener()->getOptions('route1',$this->getRoute(array('priority'=>0.5)));
        $this->assertEquals(0.5,$result['priority']);
    }

    /**
     * test "sitemap = {"changefreq = weekly"}
     */
    public function testValidChangefreq()
    {
        $result=$this->getListener()->getOptions('route1',$this->getRoute(array('changefreq'=>'weekly')));
        $this->assertEquals('weekly',$result['changefreq']);
    }

    /**
     * test "sitemap = {"lastmod" = "2012-01-01 00:00:00"}
     */
    public function testValidLastmod()
    {
        $result=$this->getListener()->getOptions('route1',$this->getRoute(array('lastmod'=>'2012-01-01 00:00:00')));
        $this->assertEquals(new \DateTime('2012-01-01 00:00:00'),$result['lastmod']);
    }

    /**
     * test "sitemap = {"lastmod" = "unknown"}
     */
    public function testInvalidLastmod()
    {
        $this->setExpectedException('\InvalidArgumentException');
        $this->getListener()->getOptions('route1',$this->getRoute(array('lastmod'=>'unknown')));
    }

    /**
     * @param null $option
     * @return \Symfony\Component\Routing\Route
     */
    private function getRoute($option = null)
    {
        $route = $this->getMockBuilder('Symfony\Component\Routing\Route')
            ->setMethods(array('getOption'))
            ->disableOriginalConstructor()
            ->getMock();

        $route->expects($this->once())
            ->method('getOption')
            ->will($this->returnValue($option));

        return $route;
    }

    /**
     * @return \Symfony\Component\Routing\RouterInterface
     */
    private function getRouter()
    {
        $router = $this->getMockBuilder('Symfony\Component\Routing\RouterInterface')
        ->getMock();

        return $router;
    }

    /**
     * @return RouteAnnotationEventListener
     */
    private function getListener()
    {
        $listener = new RouteAnnotationEventListener($this->getRouter());
        return $listener;
    }


}