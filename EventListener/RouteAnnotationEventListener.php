<?php

/**
 * This file is part of the PrestaSitemapBundle
 *
 * (c) PrestaConcept <www.prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle\EventListener;

use Presta\SitemapBundle\Event\SitemapPopulateEvent;
use Presta\SitemapBundle\Service\SitemapListenerInterface;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class RouteAnnotationEventListener
 *
 * this listener allows you to use annotations to include routes in the Sitemap, just like
 * https://github.com/dreipunktnull/DpnXmlSitemapBundle
 *
 * supported parameters are:
 *
 *  lastmod: a text string that can be parsed by \DateTime
 *  changefreq: a text string that matches a constant defined in UrlConcrete
 *  priority: a number between 0 and 1
 *
 * if you don't want to specify these parameters, you can simply use
 * Route("/", name="homepage", options={"sitemap" = true })
 *
 * @author Tony Piper (tpiper@tpiper.com)
 * @license see prestaConcept license
 */
class RouteAnnotationEventListener implements SitemapListenerInterface
{
    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var array
     */
    private $defaults;

    /**
     * @param RouterInterface $router
     * @param array           $defaults
     */
    public function __construct(RouterInterface $router, array $defaults)
    {
        $this->router = $router;
        $this->defaults = $defaults;
    }

    /**
     * @inheritdoc
     */
    public function populateSitemap(SitemapPopulateEvent $event)
    {
        $section = $event->getSection();

        if (is_null($section) || $section == 'default') {
            $this->addUrlsFromRoutes($event);
        }
    }

    /**
     * @param SitemapPopulateEvent $event
     *
     * @throws \InvalidArgumentException
     */
    private function addUrlsFromRoutes(SitemapPopulateEvent $event)
    {
        $collection = $this->getRouteCollection();
        $container = $event->getUrlContainer();

        foreach ($collection->all() as $name => $route) {
            $options = $this->getOptions($name, $route);

            if (!$options) {
                continue;
            }

            $section = $event->getSection() ?: 'default';
            if (isset($options['section'])) {
                $section = $options['section'];
            }

            $container->addUrl(
                $this->getUrlConcrete($name, $options),
                $section
            );
        }
    }

    /**
     * @return RouteCollection
     */
    protected function getRouteCollection()
    {
        return $this->router->getRouteCollection();
    }

    /**
     * @param string $name
     * @param Route  $route
     *
     * @return array
     * @throws \InvalidArgumentException
     */
    public function getOptions($name, Route $route)
    {
        $option = $route->getOption('sitemap');

        if ($option === null) {
            return null;
        }

        if (is_string($option)) {
            $decoded = json_decode($option, true);
            if (!json_last_error() && is_array($decoded)) {
                $option = $decoded;
            }
        }

        if (!filter_var($option, FILTER_VALIDATE_BOOLEAN) && !is_array($option)) {
            throw new \InvalidArgumentException('the sitemap option must be "true" or an array of parameters');
        }

        $options = $this->defaults;
        if (is_array($option)) {
            $options = array_merge($options, $option);
        }

        if (is_string($options['lastmod'])) {
            try {
                $options['lastmod'] = new \DateTime($options['lastmod']);
            } catch (\Exception $e) {
                throw new \InvalidArgumentException(
                    sprintf(
                        'The route %s has an invalid value "%s" specified for the "lastmod" option',
                        $name,
                        $options['lastmod']
                    ),
                    0,
                    $e
                );
            }
        }

        return $options;
    }

    /**
     * @param string $name    Route name
     * @param array  $options Node options
     *
     * @return UrlConcrete
     * @throws \InvalidArgumentException
     */
    protected function getUrlConcrete($name, $options)
    {
        try {
            return new UrlConcrete(
                $this->getRouteUri($name),
                $options['lastmod'],
                $options['changefreq'],
                $options['priority']
            );
        } catch (\Exception $e) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Invalid argument for route "%s": %s',
                    $name,
                    $e->getMessage()
                ),
                0,
                $e
            );
        }
    }

    /**
     * @param string $name   Route name
     * @param array  $params Route additional parameters
     *
     * @return string
     * @throws \InvalidArgumentException
     */
    protected function getRouteUri($name, $params = array())
    {
        // If the route needs additional parameters, we can't add it
        try {
            return $this->router->generate($name, $params, UrlGeneratorInterface::ABSOLUTE_URL);
        } catch (MissingMandatoryParametersException $e) {
            throw new \InvalidArgumentException(
                sprintf(
                    'The route "%s" cannot have the sitemap option because it requires parameters other than "%s"',
                    $name,
                    implode('", "', array_keys($params))
                ),
                0,
                $e
            );
        }
    }
}
