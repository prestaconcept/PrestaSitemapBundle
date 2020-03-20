<?php

namespace Presta\SitemapBundle\Tests\Integration\Tests;

use Symfony\Component\BrowserKit\AbstractBrowser;
use Symfony\Component\HttpFoundation\Request;

class HttpTest extends SitemapTestCase
{
    private const GET = Request::METHOD_GET;
    private const XML = 'text/xml; charset=UTF-8';

    public function testAccessSitemapWithHttp()
    {
        $web = self::createClient();

        // get sitemap index content via HTTP
        $web->request(self::GET, '/sitemap.xml');
        $index = $web->getResponse();
        $mime = $index->headers->get('Content-Type');
        self::assertSame(200, $index->getStatusCode(), 'Sitemap index response is successful');
        self::assertEquals(self::XML, $mime, 'Sitemap index response is XML');
        self::assertIndex($index->getContent());

        // get sitemap "static" section content via HTTP
        $web->request(self::GET, '/sitemap.static.xml');
        $static = $web->getResponse();
        $mime = $static->headers->get('Content-Type');
        self::assertSame(200, $static->getStatusCode(), 'Sitemap "static" section response is successful');
        self::assertEquals(self::XML, $mime, 'Sitemap "static" section response is XML');
        self::assertStaticSection($static->getContent());

        // get sitemap "blog" section content via HTTP
        $web->request(self::GET, '/sitemap.blog.xml');
        $blog = $web->getResponse();
        $mime = $blog->headers->get('Content-Type');
        self::assertSame(200, $blog->getStatusCode(), 'Sitemap "blog" section response is successful');
        self::assertEquals(self::XML, $mime, 'Sitemap "blog" section response is XML');
        self::assertBlogSection($blog->getContent());
    }
}
