<?php

namespace Presta\SitemapBundle\Routing;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class SitemapRoutingLoader implements LoaderInterface
{
    /**
     * @var string
     */
    private $sitemapFilePrefix;

    /**
     * @param string $sitemapFilePrefix
     */
    public function __construct($sitemapFilePrefix)
    {
        $this->sitemapFilePrefix = $sitemapFilePrefix;
    }

    /**
     * @param mixed $resource
     * @param null $type
     * @return RouteCollection
     */
    public function load($resource, $type = null)
    {
        $routes = new RouteCollection();

        // prepare a sitemap_index route
        $indexRoute = $this->getIndexRoute();
        $routes->add('PrestaSitemapBundle_index', $indexRoute);

        // prepare a sitemap_section route
        $sectionRoute = $this->getSectionRoute();
        $routes->add('PrestaSitemapBundle_section', $sectionRoute);

        return $routes;
    }

    /**
     * @param mixed $resource
     * @param string $type
     * @return bool
     */
    public function supports($resource, $type = null)
    {
        return 'presta_sitemap' === $type;
    }

    public function getResolver()
    {
    }

    /**
     * @param LoaderResolverInterface $resolver
     */
    public function setResolver(LoaderResolverInterface $resolver)
    {
    }

    /**
     * @return Route
     */
    private function getIndexRoute()
    {
        $pattern = '/' . $this->sitemapFilePrefix . '.{_format}';
        $defaults = array('_controller' => 'PrestaSitemapBundle:Sitemap:index');
        $requirements = array('_format' => 'xml');
        $route = new Route($pattern, $defaults, $requirements);
        return $route;
    }

    /**
     * @return Route
     */
    private function getSectionRoute()
    {
        $pattern = '/' . $this->sitemapFilePrefix . '.{name}.{_format}';
        $defaults = array('_controller' => 'PrestaSitemapBundle:Sitemap:section');
        $requirements = array('_format' => 'xml');
        $route = new Route($pattern, $defaults, $requirements);
        return $route;
    }
}