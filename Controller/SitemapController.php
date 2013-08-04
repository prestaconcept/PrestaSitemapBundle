<?php

/*
 * This file is part of the prestaSitemapPlugin package.
 * (c) David Epely <depely@prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle\Controller;

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
     * //TODO: implement basic urlset rendering
     * //TODO: implement sitemapindex composed by sitemapindex
     *
     * @param $_format
     * @return Response
     */
    public function indexAction()
    {
        $sitemapindex = $this->get('presta_sitemap.generator')->fetch('root');

        if (!$sitemapindex) {
            throw $this->createNotFoundException();
        }

        $response = Response::create($sitemapindex->toXml());
        $response->headers->set('Content-Type', 'text/xml');
        $response->setPublic();
        $response->setClientTtl($this->getTtl());

        return $response;
    }

    /**
     * list urls of a section
     *
     * @param string
     * @return Response
     */
    public function sectionAction($name)
    {
        $section = $this->get('presta_sitemap.generator')->fetch($name);

        if (!$section) {
            throw $this->createNotFoundException();
        }

        $response = Response::create($section->toXml());
        $response->headers->set('Content-Type', 'text/xml');
        $response->setPublic();
        $response->setClientTtl($this->getTtl());

        return $response;
    }

    /**
     * Time to live of the response in seconds
     * @return int
     */
    protected function getTtl()
    {
        return $this->container->getParameter('presta_sitemap.timetolive');
    }
}
