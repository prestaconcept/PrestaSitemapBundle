<?php

/*
 * This file is part of the PrestaSitemapBundle package.
 *
 * (c) PrestaConcept <https://prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle\Tests\Integration\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route as RouteAnnotation;
use Symfony\Component\Routing\Attribute\Route as RouteAttribute;

final class StaticController
{
    /**
     * @RouteAnnotation("", name="home", options={"sitemap"={"section"="static"}})
     */
    #[RouteAttribute(path: '', name: 'home', options: ['sitemap' => ['section' => 'static']])]
    public function home(): Response
    {
        return new Response(__FUNCTION__);
    }

    public function contact(): Response
    {
        return new Response(__FUNCTION__);
    }

    public function company(): Response
    {
        return new Response(__FUNCTION__);
    }

    public function about(): Response
    {
        return new Response(__FUNCTION__);
    }
}
