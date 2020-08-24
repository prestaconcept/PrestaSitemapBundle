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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Provides action to render sitemap files
 *
 * @author David Epely <depely@prestaconcept.net>
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

    /**
     * @param int $ttl
     */
    public function __construct(GeneratorInterface $generator, $ttl)
    {
        $this->generator = $generator;
        $this->ttl = $ttl;
    }

    /**
     * list sitemaps
     *
     * @return Response
     */
    public function indexAction()
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
    public function sectionAction($name)
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

    /**
     * Time to live of the response in seconds
     *
     * @return int
     * @deprecated since v2.3.0
     * @codeCoverageIgnore
     */
    protected function getTtl()
    {
        @trigger_error(__METHOD__ . ' method is deprecated since v2.3.0', E_USER_DEPRECATED);

        return $this->ttl;
    }
}
