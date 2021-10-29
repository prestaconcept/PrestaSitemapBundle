<?php

/*
 * This file is part of the PrestaSitemapBundle package.
 *
 * (c) PrestaConcept <https://prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle\Service;

use Presta\SitemapBundle\Sitemap\Urlset;
use Presta\SitemapBundle\Sitemap\XmlConstraint;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Sitemap generator.
 */
class Generator extends AbstractGenerator implements GeneratorInterface
{
    /**
     * @var UrlGeneratorInterface
     */
    protected $router;

    /**
     * @param EventDispatcherInterface $dispatcher
     * @param UrlGeneratorInterface    $router
     * @param int|null                 $itemsBySet
     */
    public function __construct(
        EventDispatcherInterface $dispatcher,
        UrlGeneratorInterface $router,
        int $itemsBySet = null
    ) {
        parent::__construct($dispatcher, $itemsBySet, $router);

        $this->router = $router;
    }

    /**
     * @inheritdoc
     */
    public function fetch(string $name): ?XmlConstraint
    {
        if ('root' === $name) {
            $this->populate();

            return $this->getRoot();
        }

        $this->populate($name);

        if (array_key_exists($name, $this->urlsets)) {
            return $this->urlsets[$name];
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    protected function newUrlset(string $name, \DateTimeInterface $lastmod = null): Urlset
    {
        return new Urlset(
            $this->router->generate(
                'PrestaSitemapBundle_section',
                ['name' => $name, '_format' => 'xml'],
                UrlGeneratorInterface::ABSOLUTE_URL
            ),
            $lastmod
        );
    }
}
