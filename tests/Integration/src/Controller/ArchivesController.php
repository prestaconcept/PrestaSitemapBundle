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
use Symfony\Component\Routing\Annotation\Route;

final class ArchivesController
{
    /**
     * @Route("/archive", name="archive")
     */
    #[Route(path: '/archive', name: 'archive')]
    public function archive(): Response
    {
        return new Response(__FUNCTION__);
    }
}
