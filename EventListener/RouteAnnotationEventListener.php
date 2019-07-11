<?php

/**
 * This file is part of the PrestaSitemapBundle package.
 *
 * (c) PrestaConcept <www.prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle\EventListener;

use Presta\SitemapBundle\Event\SitemapPopulateEvent;
use Presta\SitemapBundle\Routing\RouteOptionParser;
use Presta\SitemapBundle\Service\UrlContainerInterface;
use Presta\SitemapBundle\Sitemap\Url\GoogleMultilangUrlDecorator;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;

/**
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
 */
class RouteAnnotationEventListener implements EventSubscriberInterface
{
    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var string
     */
    private $defaultSection;

    /**
     * @var array
     */
    private $defaultOptions;

    /**
     * @param RouterInterface $router
     * @param string          $defaultSection
     * @param array           $defaultOptions
     */
    public function __construct(
        RouterInterface $router,
        $defaultSection,
        $defaultOptions = [
            'lastmod' => null,
            'changefreq' => null,
            'priority' => null,
            'default_locale' => null,
            'locales' => null,
        ]
    ) {
        $this->router = $router;
        $this->defaultSection = $defaultSection;
        $this->defaultOptions = $defaultOptions;
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [
            SitemapPopulateEvent::ON_SITEMAP_POPULATE => ['registerRouteAnnotation', 0],
        ];
    }

    /**
     * @param SitemapPopulateEvent $event
     */
    public function registerRouteAnnotation(SitemapPopulateEvent $event)
    {
        $this->addUrlsFromRoutes($event->getUrlContainer(), $event->getSection());
    }

    /**
     * @param SitemapPopulateEvent $event
     *
     * @throws \InvalidArgumentException
     */
    private function addUrlsFromRoutes(UrlContainerInterface $container, ?string $section)
    {
        $collection = $this->getRouteCollection();

        foreach ($collection->all() as $name => $route) {
            $options = RouteOptionParser::parse($name, $route);
            if (!$options) {
                continue;
            }

            $routeSection = $options['section'] ?? $this->defaultSection;
            if ($section !== null && $routeSection !== $section) {
                continue;
            }

            if ($this->defaultOptions['default_locale']) {
                if (strpos($name, $this->defaultOptions['default_locale']) === false) {
                    continue;
                }

                $name = preg_replace('/[a-z]+__RG__/', '', $name);
            }

            $container->addUrl(
                $this->getUrlConcrete($name, $options),
                $routeSection
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
     * @deprecated since 2.3.0, use @link RouteOptionParser::parse instead
     *
     * @param string $name
     * @param Route  $route
     *
     * @return array|null
     * @throws \InvalidArgumentException
     */
    public function getOptions($name, Route $route)
    {
        @trigger_error(
            sprintf(
                '%s is deprecated since 2.3.0 and will be removed in 3.0.0, use %s::%s instead',
                __METHOD__,
                RouteOptionParser::class,
                'parse'
            ),
            E_USER_DEPRECATED
        );

        return RouteOptionParser::parse($name, $route);
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
            $params = [];

            if ($options['default_locale']) {
                $params['_locale'] = $options['default_locale'];
            }

            $url = new UrlConcrete(
                $this->getRouteUri($name, $params),
                $options['lastmod'],
                $options['changefreq'],
                $options['priority']
            );

            if ($options['locales'] && is_array($options['locales'])) {
                $url = new GoogleMultilangUrlDecorator($url);

                foreach ($options['locales'] as $locale) {
                    $params['_locale'] = $locale;

                    $url->addLink($this->getRouteUri($name, $params), $locale);
                }
            }

            return $url;
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
    protected function getRouteUri($name, $params = [])
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
