<?php

/*
 * This file is part of the PrestaSitemapBundle package.
 *
 * (c) PrestaConcept <https://prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle\Controller;

use Presta\SitemapBundle\Service\GeneratorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Provides action to render sitemap files
 */
class SitemapController
{
    /**
     * @var GeneratorInterface
     */
    private $generator;

    /**
     * Time to live of the response in seconds
     *
     * @var int
     */
    private $ttl;

    public function __construct(GeneratorInterface $generator, int $ttl)
    {
        $this->generator = $generator;
        $this->ttl = $ttl;
    }

    /**
     * list sitemaps
     *
     * @return Response
     */
    public function indexAction(): Response
    {
        $sitemapindex = $this->generator->fetch('root');

        if (!$sitemapindex) {
            throw new NotFoundHttpException('Not found');
        }

        $response = new Response($sitemapindex->toXml());
        $response->headers->set('Content-Type', 'text/xml');
        $response->setPublic();
        $response->setClientTtl($this->ttl);

        return $response;
    }

    /**
     * list urls of a section
     *
     * @param string $name
     *
     * @return Response
     */
    public function sectionAction(string $name): Response
    {
        $section = $this->generator->fetch($name);

        if (!$section) {
            throw new NotFoundHttpException('Not found');
        }

        $response = new Response($section->toXml());
        $response->headers->set('Content-Type', 'text/xml');
        $response->setPublic();
        $response->setClientTtl($this->ttl);

        return $response;
    }
}
