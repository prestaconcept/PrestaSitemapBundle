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
     * list sitemaps
     *
     * @return Response
     */
    public function indexAction()
    {
        $sitemapindex = $this->getGenerator()->fetch('root');

        if (!$sitemapindex) {
            throw $this->createNotFoundException();
        }

        $response = Response::create($sitemapindex->toXml());
        $response->setPublic();
        $response->setClientTtl($this->getTtl());

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
        $section = $this->getGenerator()->fetch($name);

        if (!$section) {
            throw $this->createNotFoundException();
        }

        $response = Response::create($section->toXml());
        $response->setPublic();
        $response->setClientTtl($this->getTtl());

        return $response;
    }

    /**
     * Time to live of the response in seconds
     *
     * @return int
     */
    protected function getTtl()
    {
        return $this->container->getParameter('presta_sitemap.timetolive');
    }

    /**
     * @return GeneratorInterface
     */
    private function getGenerator()
    {
        return $this->get('presta_sitemap.generator');
    }
}
