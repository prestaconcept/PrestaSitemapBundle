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

final class BlogController
{
    /**
     * @RouteAnnotation("/blog", name="blog_read", options={"sitemap"={"section"="blog"}})
     */
    #[RouteAttribute(path: '/blog', name: 'blog_read', options: ['sitemap' => ['section' => 'blog']])]
    public function read(): Response
    {
        return new Response(__FUNCTION__);
    }

    /**
     * @RouteAnnotation("/blog/{slug}", name="blog_post")
     */
    #[RouteAttribute(path: '/blog/{slug}', name: 'blog_post')]
    public function post(string $slug): Response
    {
        return new Response(__FUNCTION__ . ':' . $slug);
    }
}
