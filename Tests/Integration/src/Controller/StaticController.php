<?php

namespace Presta\SitemapBundle\Tests\Integration\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class StaticController
{
    /**
     * @Route("", name="home", options={"sitemap"={"section"="static"}})
     */
    public function home(): Response
    {
        return Response::create(__FUNCTION__);
    }

    public function contact(): Response
    {
        return Response::create(__FUNCTION__);
    }

    public function company(): Response
    {
        return Response::create(__FUNCTION__);
    }
}
