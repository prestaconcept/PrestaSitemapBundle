<?php

namespace Presta\SitemapBundle\Tests\Integration\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class FormatController
{
    /**
     * @Route("/annotation", name="annotation", options={"sitemap"={"section"="static"}})
     */
    public function annotation(): Response
    {
        return Response::create(__FUNCTION__);
    }

    public function yaml(): Response
    {
        return Response::create(__FUNCTION__);
    }

    public function xml(): Response
    {
        return Response::create(__FUNCTION__);
    }
}
