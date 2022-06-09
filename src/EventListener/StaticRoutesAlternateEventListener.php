<?php

/*
 * This file is part of the PrestaSitemapBundle package.
 *
 * (c) PrestaConcept <https://prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle\EventListener;

use Presta\SitemapBundle\Event\SitemapAddUrlEvent;
use Presta\SitemapBundle\Sitemap\Url\GoogleMultilangUrlDecorator;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Listen to "presta_sitemap.add_url" event.
 * Decorate translatable Url with multi-lang alternatives.
 * Support both Symfony translated routes & JMSI18nRoutingBundle.
 *
 * @phpstan-type Options array{
 *     i18n: string,
 *     default_locale: string,
 *     locales: array<string>
 * }
 */
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
     * @var Options
     */
    private $options;

    /**
     * @param UrlGeneratorInterface $router
     * @param Options               $options
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
            SitemapAddUrlEvent::class => 'addAlternate',
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
