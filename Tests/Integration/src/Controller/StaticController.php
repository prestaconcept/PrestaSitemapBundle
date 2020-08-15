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
}
