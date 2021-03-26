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

use Presta\SitemapBundle\Event\SitemapAddUrlEvent;
use Presta\SitemapBundle\Event\SitemapPopulateEvent;
use Presta\SitemapBundle\Routing\RouteOptionParser;
use Presta\SitemapBundle\Service\UrlContainerInterface;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;

/**
 * This listener iterate over configured routes, and register allowed URLs to sitemap.
 */
class RouteAnnotationEventListener implements EventSubscriberInterface
{
    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var string
     */
    private $defaultSection;

    public function __construct(
        RouterInterface $router,
        EventDispatcherInterface $eventDispatcher,
        string $defaultSection
    ) {
        $this->router = $router;
        $this->dispatcher = $eventDispatcher;
        $this->defaultSection = $defaultSection;
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
     * @param UrlContainerInterface $container
     * @param string|null           $section
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

            $event = new SitemapAddUrlEvent($name, $options);
            $this->dispatcher->dispatch($event, SitemapAddUrlEvent::NAME);

            if (!$event->shouldBeRegistered()) {
                continue;
            }

            $container->addUrl(
                $event->getUrl() ?? $this->getUrlConcrete($name, $options),
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
