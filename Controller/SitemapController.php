<?php

/**
 * This file is part of the PrestaSitemapBundle package.
 *
 * (c) PrestaConcept <www.prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle\Controller;

use Presta\SitemapBundle\Service\GeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Provides action to render sitemap files
 *
 * @author David Epely <depely@prestaconcept.net>
 */
class SitemapController extends Controller
{
    /**
     * @var GeneratorInterface
     */
    private $generator;

    /**
     * @var int
     */
    private $ttl;

    /**
     * @param GeneratorInterface $generator
     * @param int                $ttl
     */
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
    public function root(): Response
    {
        $sitemapindex = $this->generator->fetch('root');

        if (!$sitemapindex) {
            throw $this->createNotFoundException();
        }

        $response = Response::create($sitemapindex->toXml());
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
    public function section(string $name): Response
    {
        $section = $this->generator->fetch($name);

        if (!$section) {
            throw $this->createNotFoundException();
        }

        $response = Response::create($section->toXml());
        $response->setPublic();
        $response->setClientTtl($this->ttl);

        return $response;
    }
}
