<?php

namespace Presta\SitemapBundle\Tests\Integration\Tests\Sitemap;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

final class LiveTest extends WebTestCase
{
    private const GET = Request::METHOD_GET;
    private const XML = 'text/xml; charset=UTF-8';

    protected function setUp(): void
    {
        DumpUtil::clean();
    }

    public function testAccessSitemapWithHttp()
    {
        $web = self::createClient();

        // get sitemap index content via HTTP
        $web->request(self::GET, '/sitemap.xml');
        $index = $web->getResponse();
        self::assertSame(200, $index->getStatusCode(), 'Sitemap index response is successful');
        self::assertEquals(self::XML, $index->headers->get('Content-Type'),
            'Sitemap index response is XML'
        );
        AssertUtil::assertIndex($index->getContent());

        // get sitemap "static" section content via HTTP
        $web->request(self::GET, '/sitemap.static.xml');
        $static = $web->getResponse();
        self::assertSame(200, $static->getStatusCode(), 'Sitemap "static" section response is successful');
        self::assertEquals(self::XML, $static->headers->get('Content-Type'),
            'Sitemap "static" section response is XML'
        );
        AssertUtil::assertStaticSection($static->getContent());

        // get sitemap "blog" section content via HTTP
        $web->request(self::GET, '/sitemap.blog.xml');
        $blog = $web->getResponse();
        self::assertSame(200, $blog->getStatusCode(), 'Sitemap "blog" section response is successful');
        self::assertEquals(self::XML, $blog->headers->get('Content-Type'),
            'Sitemap "blog" section response is XML'
        );
        AssertUtil::assertBlogSection($blog->getContent());
    }
}
