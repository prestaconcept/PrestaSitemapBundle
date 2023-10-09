<?php

/*
 * This file is part of the PrestaSitemapBundle package.
 *
 * (c) PrestaConcept <https://prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle\Event;

use LogicException;
use Presta\SitemapBundle\Service\UrlContainerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Event called whenever a sitemap build is requested.
 *
 * Subscribe to this event if :
 *  - you want to register non-static routes
 */
class SitemapPopulateEvent extends Event
{
    /**
     * @Event("Presta\SitemapBundle\Event\SitemapPopulateEvent")
     * @deprecated since presta/sitemap-bundle 3.3, use `SitemapPopulateEvent::class` instead.
     */
    public const ON_SITEMAP_POPULATE = 'presta_sitemap.populate';

    /**
     * @var UrlContainerInterface
     */
    protected $urlContainer;

    /**
     * Allows creating EventListeners for particular sitemap sections, used when dumping
     * @var string|null
     */
    protected $section;

    /**
     * @var UrlGeneratorInterface|null
     */
    protected $urlGenerator;

    /**
     * @param UrlContainerInterface      $urlContainer
     * @param string|null                $section
     * @param UrlGeneratorInterface|null $urlGenerator
     */
    public function __construct(
        UrlContainerInterface $urlContainer,
        $urlGenerator = null,
        $section = null
    ) {
        if (
            (\is_null($urlGenerator) || \is_string($urlGenerator))
            && (\is_null($section) || $section instanceof UrlGeneratorInterface)
        ) {
            $tmpUrlGenerator = $section;
            $section = $urlGenerator;
            $urlGenerator = $tmpUrlGenerator;
            @\trigger_error(
                \sprintf(
                    '%s will change in 4.0, the argument #2 will be %s $urlGenerator.',
                    __METHOD__,
                    UrlGeneratorInterface::class
                ),
                \E_USER_DEPRECATED
            );
        }
        if (!\is_null($urlGenerator) && !$urlGenerator instanceof UrlGeneratorInterface) {
            throw new \TypeError(\sprintf(
                '%s(): Argument #2 ($urlGenerator) must be of type %s, %s given.',
                __METHOD__,
                UrlGeneratorInterface::class,
                \is_object($urlGenerator) ? \get_class($urlGenerator) : \gettype($urlGenerator)
            ));
        }
        if (!\is_null($section) && !\is_string($section)) {
            throw new \TypeError(\sprintf(
                '%s(): Argument #3 ($itemsBySet) must be of type ?string, %s given.',
                __METHOD__,
                \is_object($section) ? \get_class($section) : \gettype($section)
            ));
        }

        $this->urlContainer = $urlContainer;
        $this->section = $section;
        $this->urlGenerator = $urlGenerator;
        if ($urlGenerator === null) {
            @trigger_error(
                'Not injecting the $urlGenerator in ' . __CLASS__ . ' is deprecated and will be required in 4.0.',
                \E_USER_DEPRECATED
            );
        }
    }

    /**
     * @return UrlContainerInterface
     */
    public function getUrlContainer(): UrlContainerInterface
    {
        return $this->urlContainer;
    }

    /**
     * Section to be processed, null means any
     *
     * @return null|string
     */
    public function getSection(): ?string
    {
        return $this->section;
    }

    public function getUrlGenerator(): UrlGeneratorInterface
    {
        if (!$this->urlGenerator) {
            throw new LogicException('UrlGenerator was not set.');
        }

        return $this->urlGenerator;
    }
}
