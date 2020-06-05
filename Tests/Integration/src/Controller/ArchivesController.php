<?php

namespace Presta\SitemapBundle\Tests\Integration\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class ArchivesController
{
    /**
     * @Route("/archive", name="archive")
     */
    public function archive(): Response
    {
        return new Response(__FUNCTION__);
    }
}
