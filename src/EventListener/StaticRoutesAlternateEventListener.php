<?php

namespace Presta\SitemapBundle\EventListener;

use Presta\SitemapBundle\Event\SitemapAddUrlEvent;
use Presta\SitemapBundle\Sitemap\Url\GoogleMultilangUrlDecorator;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class StaticRoutesAlternateEventListener implements EventSubscriberInterface
{
    private const TRANSLATED_ROUTE_NAME_STRATEGIES = [
        'symfony' => '/^(?P<name>.+)\.(?P<locale>%locales%)$/',
        'jms' => '/^(?P<locale>%locales%)__RG__(?P<name>.+)$/',
    ];

    /**
     * @var UrlGeneratorInterface
     */
    private $router;

    /**
     * @var array<string, mixed>
     */
    private $options;

    /**
     * @param UrlGeneratorInterface $router
     * @param array<string, mixed>  $options
     */
    public function __construct(UrlGeneratorInterface $router, array $options)
    {
        $this->router = $router;
        $this->options = $options;
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            SitemapAddUrlEvent::NAME => 'addAlternate',
        ];
    }

    public function addAlternate(SitemapAddUrlEvent $event): void
    {
        $name = $event->getRoute();
        $options = $event->getOptions();

        $info = $this->getTranslatedRouteInfo($name);
        if ($info === null) {
            return; // not a supported translated route
        }

        [$translatedName, $locale] = $info;

        if ($locale !== $this->options['default_locale']) {
            // route is translated, but we are on the non default locale route, should be skipped
            $event->preventRegistration();

            return;
        }

        $url = new GoogleMultilangUrlDecorator(
            new UrlConcrete(
                $this->generateTranslatedRouteUrl($translatedName, $locale),
                $options['lastmod'],
                $options['changefreq'],
                $options['priority']
            )
        );
        foreach ($this->options['locales'] as $alternate) {
            $url->addLink($this->generateTranslatedRouteUrl($translatedName, $alternate), $alternate);
        }

        $event->setUrl($url);
    }

    /**
     * @param string $name
     *
     * @return array{0: string, 1: string}|null
     */
    private function getTranslatedRouteInfo(string $name): ?array
    {
        $pattern = self::TRANSLATED_ROUTE_NAME_STRATEGIES[$this->options['i18n']] ?? '';
        $pattern = \str_replace('%locales%', \implode('|', $this->options['locales']), $pattern);

        if (!\preg_match($pattern, $name, $matches)) {
            return null; // route name do not match translated route name pattern, skip
        }

        return [$matches['name'], $matches['locale']];
    }

    private function generateTranslatedRouteUrl(string $name, string $locale): string
    {
        return $this->router->generate($name, ['_locale' => $locale], UrlGeneratorInterface::ABSOLUTE_URL);
    }
}
