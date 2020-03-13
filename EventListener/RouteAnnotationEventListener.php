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
    private $alternateSection;

    /**
     * @param RouterInterface $router
     * @param string          $defaultSection
     * @param array           $alternateSection
     */
    public function __construct(RouterInterface $router, ?string $defaultSection, ?array $alternateSection = null)
    {
        $this->router = $router;
        $this->defaultSection = $defaultSection;
        $this->alternateSection = $alternateSection;
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
            $options = $this->getOptions($name, $route);
            if (!$options) {
                continue;
            }

            $routeSection = $options['section'] ?? $this->defaultSection;

            if ($section !== null && $routeSection !== $section) {
                continue;
            }

            if ($this->alternateSection) {
                if ($this->alternateSection['default_locale']) {
                    if (strpos($name, $this->alternateSection['default_locale']) === false) {
                        continue;
                    }

                    switch ($this->alternateSection['i18n']) {
                        case 'symfony':
                            // Replace route_name.en or route_name.it into route_name
                            $name = preg_replace("/\.[a-z]+/", '', $name);
                            break;
                        case 'jms':
                            // Replace en__RG__route_name or it__RG__route_name into route_name
                            $name = preg_replace("/[a-z]+__RG__/", '', $name);
                            break;
                    }
                }

                $options = array_merge($options, $this->alternateSection);

                $container->addUrl(
                    $this->getMultilangUrl($name, $options),
                    $section
                );
            } else {
                $container->addUrl(
                    $this->getUrlConcrete($name, $options),
                    $routeSection
                );
            }
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

        if (!is_array($option) && !is_bool($option)) {
            $bool = filter_var($option, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

            if (null === $bool) {
                throw new \InvalidArgumentException(
                    sprintf(
                        'The sitemap option must be of type "boolean" or "array", got "%s"',
                        $option
                    )
                );
            }

            $option = $bool;
        }

        if (!$option) {
            return null;
        }

        $options = [
            'lastmod' => null,
            'changefreq' => null,
            'priority' => null,
        ];
        if (is_array($option)) {
            $options = array_merge($options, $option);
        }

        if (is_string($options['lastmod'])) {
            try {
                $options['lastmod'] = new \DateTimeImmutable($options['lastmod']);
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
     * @param array  $params  Optional route params
     *
     * @return UrlConcrete
     */
    protected function getUrlConcrete($name, $options, $params = [])
    {
        try {
            return new UrlConcrete(
                $this->getRouteUri($name, $params),
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
     * @param string $name    Route name
     * @param array  $options Node options
     *
     * @throws \InvalidArgumentException
     * @return UrlConcrete
     */
    protected function getMultilangUrl($name, $options)
    {
        $params = [];

        if ($options['default_locale']) {
            $params['_locale'] = $options['default_locale'];
        }

        $url = $this->getUrlConcrete($name, $options, $params);

        if ($options['locales'] && is_array($options['locales'])) {
            $url = new GoogleMultilangUrlDecorator($url);

            foreach ($options['locales'] as $locale) {
                $params['_locale'] = $locale;

                $url->addLink($this->getRouteUri($name, $params), $locale);
            }
        }

        return $url;
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
