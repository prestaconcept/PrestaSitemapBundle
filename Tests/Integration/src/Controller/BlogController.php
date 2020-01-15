<?php

namespace Presta\SitemapBundle\Tests\Integration\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class BlogController
{
    /**
     * @Route("/blog", name="blog_read")
     */
    public function read(): Response
    {
        return Response::create(__FUNCTION__);
    }

    /**
     * @Route("/blog/{slug}", name="blog_post")
     */
    public function post(string $slug): Response
    {
        return Response::create(__FUNCTION__ . ':' . $slug);
    }
}
