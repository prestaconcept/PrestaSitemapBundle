<?php

/**
 * This file is part of the PrestaSitemapBundle package.
 *
 * (c) PrestaConcept <www.prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle\Tests\Unit\EventListener;

use PHPUnit\Framework\TestCase;
use Presta\SitemapBundle\EventListener\RouteAnnotationEventListener;

/**
* Manage sitemaps listing
*
* @author David Epely <depely@prestaconcept.net>
*/
class RouteAnnotationEventListenerTest extends TestCase
{
    /**
     * test no "sitemap" annotation
     */
    public function testNoAnnotation()
    {
        self::assertEquals(null, $this->getListener()->getOptions('route1', $this->getRoute(null)), 'sitemap = null returns null');
    }

    /**
     * test "sitemap"="anything" annotation
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidSitemapArbitrary()
    {
        self::assertEquals(-1, $this->getListener()->getOptions('route1', $this->getRoute('anything')), 'sitemap = "anything" throws an exception');
    }

    /**
     * test "sitemap"=false annotation
     */
    public function testSitemapFalse()
    {
        self::assertNull($this->getListener()->getOptions('route1', $this->getRoute(false)), 'sitemap = false returns null');
    }

    /**
     * test "sitemap"=true
     */
    public function testDefaultAnnotation()
    {
        $result=$this->getListener()->getOptions('route1', $this->getRoute(true));
        self::assertArrayHasKey('priority', $result);
        self::assertArrayHasKey('changefreq', $result);
        self::assertArrayHasKey('lastmod', $result);
        self::assertNull($result['priority']);
        self::assertNull($result['changefreq']);
        self::assertNull($result['lastmod']);
    }

    /**
     * test "sitemap = {"priority" = "0.5"}
     */
    public function testValidPriority()
    {
        $result=$this->getListener()->getOptions('route1', $this->getRoute(['priority' => 0.5]));
        self::assertEquals(0.5, $result['priority']);
    }

    /**
     * test "sitemap = {"changefreq = weekly"}
     */
    public function testValidChangefreq()
    {
        $result=$this->getListener()->getOptions('route1', $this->getRoute(['changefreq' => 'weekly']));
        self::assertEquals('weekly', $result['changefreq']);
    }

    /**
     * test "sitemap = {"lastmod" = "2012-01-01 00:00:00"}
     */
    public function testValidLastmod()
    {
        $result=$this->getListener()->getOptions('route1', $this->getRoute(['lastmod' => '2012-01-01 00:00:00']));
        self::assertEquals(new \DateTime('2012-01-01 00:00:00'), $result['lastmod']);
    }

    /**
     * test "sitemap = {"lastmod" = "unknown"}
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidLastmod()
    {
        $this->getListener()->getOptions('route1', $this->getRoute(['lastmod' => 'unknown']));
    }

    /**
     * @param  null                             $option
     * @return \Symfony\Component\Routing\Route
     */
    private function getRoute($option = null)
    {
        $route = $this->getMockBuilder('Symfony\Component\Routing\Route')
            ->setMethods(['getOption'])
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
        return new RouteAnnotationEventListener(
            $this->getRouter(),
            'default'
        );
    }
}
