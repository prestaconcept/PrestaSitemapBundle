<?php

/**
 * This file is part of the PrestaSitemapBundle package.
 *
 * (c) PrestaConcept <www.prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle\Test\Sitemap;

use Presta\SitemapBundle\EventListener\RouteAnnotationEventListener;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;

/**
* Manage sitemaps listing
*
* @author David Epely <depely@prestaconcept.net>
*/
class RouteAnnotationEventListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * test no "sitemap" annotation
     */
    public function testNoAnnotation()
    {
        $this->assertEquals(null, $this->getListener()->getOptions('route1', $this->getRoute(null)), 'sitemap = null returns null');
    }

    /**
     * test "sitemap"="anything" annotation
     */
    public function testInvalidSitemapArbitrary()
    {
        $this->setExpectedException('InvalidArgumentException');
        $this->assertEquals(-1, $this->getListener()->getOptions('route1', $this->getRoute('anything')), 'sitemap = "anything" throws an exception');
    }

    /**
     * test "sitemap"=false annotation
     */
    public function testSitemapFalse()
    {
        $this->assertNull($this->getListener()->getOptions('route1', $this->getRoute(false)), 'sitemap = false returns null');
    }

    /**
     * test "sitemap"=true
     */
    public function testDefaultAnnotation()
    {
        $result=$this->getListener()->getOptions('route1', $this->getRoute(true));
        $this->assertArrayHasKey('priority', $result);
        $this->assertArrayHasKey('changefreq', $result);
        $this->assertArrayHasKey('lastmod', $result);
        $this->assertNull($result['priority']);
        $this->assertNull($result['changefreq']);
        $this->assertNull($result['lastmod']);
    }

    /**
     * test "sitemap = {"priority" = "0.5"}
     */
    public function testValidPriority()
    {
        $result=$this->getListener()->getOptions('route1', $this->getRoute(array('priority'=>0.5)));
        $this->assertEquals(0.5, $result['priority']);
    }

    /**
     * test "sitemap = {"changefreq = weekly"}
     */
    public function testValidChangefreq()
    {
        $result=$this->getListener()->getOptions('route1', $this->getRoute(array('changefreq'=>'weekly')));
        $this->assertEquals('weekly', $result['changefreq']);
    }

    /**
     * test "sitemap = {"lastmod" = "2012-01-01 00:00:00"}
     */
    public function testValidLastmod()
    {
        $result=$this->getListener()->getOptions('route1', $this->getRoute(array('lastmod'=>'2012-01-01 00:00:00')));
        $this->assertEquals(new \DateTime('2012-01-01 00:00:00'), $result['lastmod']);
    }

    /**
     * test "sitemap = {"lastmod" = "unknown"}
     */
    public function testInvalidLastmod()
    {
        $this->setExpectedException('InvalidArgumentException');
        $this->getListener()->getOptions('route1', $this->getRoute(array('lastmod'=>'unknown')));
    }

    /**
     * @param  null                             $option
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
        $listener = new RouteAnnotationEventListener(
            $this->getRouter(),
            array(
                'priority' => 1,
                'changefreq' => UrlConcrete::CHANGEFREQ_DAILY,
                'lastmod' => 'now',
            )
        );

        return $listener;
    }
}
