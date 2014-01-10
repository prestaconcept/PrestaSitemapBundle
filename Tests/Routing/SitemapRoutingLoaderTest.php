<?php

namespace Presta\SitemapBundle\Routing;

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class SitemapRoutingLoaderTest extends \PHPUnit_Framework_TestCase
{
    public function testLoad()
    {
        $requirements = array('_format' => 'xml');

        $expected = new RouteCollection();
        $indexExpected = new Route('/prefix.{_format}', array('_controller' => 'PrestaSitemapBundle:Sitemap:index'), $requirements);
        $sectionExpected = new Route('/prefix.{name}.{_format}', array('_controller' => 'PrestaSitemapBundle:Sitemap:section'), $requirements);

        $expected->add('PrestaSitemapBundle_index', $indexExpected);
        $expected->add('PrestaSitemapBundle_section', $sectionExpected);

        $loader = new SitemapRoutingLoader('prefix');
        $this->assertEquals($expected, $loader->load('ignored'));
    }
}
 