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

final class ArchivesController
{
    /**
     * @RouteAnnotation("/archive", name="archive")
     */
    #[RouteAttribute(path: '/archive', name: 'archive')]
    public function archive(): Response
    {
        return new Response(__FUNCTION__);
    }
}
