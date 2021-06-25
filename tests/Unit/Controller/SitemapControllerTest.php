<?php

/*
 * This file is part of the PrestaSitemapBundle package.
 *
 * (c) PrestaConcept <https://prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle\Tests\Unit\Controller;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Presta\SitemapBundle\Controller\SitemapController;
use Presta\SitemapBundle\Service\GeneratorInterface;
use Presta\SitemapBundle\Sitemap\Sitemapindex;
use Presta\SitemapBundle\Sitemap\Urlset;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SitemapControllerTest extends TestCase
{
    private const TTL = 3600;

    /**
     * @var GeneratorInterface|MockObject
     */
    private $generator;

    public function setUp(): void
    {
        $this->generator = $this->createMock(GeneratorInterface::class);
    }

    public function testIndexSuccessful(): void
    {
        /** @var Sitemapindex|MockObject $index */
        $index = $this->createMock(Sitemapindex::class);
        $index->method('toXml')
            ->willReturn('<index/>');

        $this->generator->method('fetch')
            ->willReturn($index);

        $response = $this->controller()->indexAction();
        self::assertSitemapResponse($response, '<index/>');
    }

    public function testIndexNotFound(): void
    {
        $this->expectException(NotFoundHttpException::class);

        $this->generator->method('fetch')
            ->with('root')
            ->willReturn(null);

        $this->controller()->indexAction();
    }

    public function testSectionSuccessful(): void
    {
        /** @var Urlset|MockObject $urlset */
        $urlset = $this->createMock(Urlset::class);
        $urlset->method('toXml')
            ->willReturn('<urlset/>');

        $this->generator->method('fetch')
            ->with('default')
            ->willReturn($urlset);

        $response = $this->controller()->sectionAction('default');
        self::assertSitemapResponse($response, '<urlset/>');
    }

    public function testSectionNotFound(): void
    {
        $this->expectException(NotFoundHttpException::class);

        $this->generator->method('fetch')
            ->with('void')
            ->willReturn(null);

        $this->controller()->sectionAction('void');
    }

    private static function assertSitemapResponse($response, string $xml): void
    {
        /** @var Response $response */
        self::assertInstanceOf(Response::class, $response,
            'Controller returned a response object'
        );
        self::assertEquals('text/xml', $response->headers->get('Content-Type'),
            'Controller returned an XML response'
        );
        self::assertTrue($response->isCacheable(),
            'Controller returned a cacheable response'
        );
        self::assertSame(self::TTL, $response->getMaxAge(),
            'Controller returned a response cacheable for ' . self::TTL . ' seconds'
        );
        self::assertSame($xml, $response->getContent(),
            'Controller returned expected content'
        );
    }

    private function controller(): SitemapController
    {
        return new SitemapController($this->generator, self::TTL);
    }
}
