<?php

/**
 * This file is part of the PrestaSitemapBundle package.
 *
 * (c) PrestaConcept <www.prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle\Tests\Unit\Controller;

use PHPUnit\Framework\TestCase;
use Presta\SitemapBundle\Controller\SitemapController;
use Presta\SitemapBundle\Service\GeneratorInterface;
use Presta\SitemapBundle\Sitemap\Sitemapindex;
use Presta\SitemapBundle\Sitemap\Urlset;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SitemapControllerTest extends TestCase
{
    private const TTL = 3600;

    /**
     * @var GeneratorInterface|ObjectProphecy
     */
    private $generator;

    public function setUp(): void
    {
        $this->generator = $this->prophesize(GeneratorInterface::class);
    }

    public function testIndexSuccessful(): void
    {
        /** @var Sitemapindex|ObjectProphecy $index */
        $index = $this->prophesize(Sitemapindex::class);
        $index->toXml()
            ->shouldBeCalledTimes(1)
            ->willReturn('<index/>');

        $this->generator->fetch('root')
            ->shouldBeCalledTimes(1)
            ->willReturn($index->reveal());

        $response = $this->controller()->indexAction();
        self::assertSitemapResponse($response, '<index/>');
    }

    public function testIndexNotFound(): void
    {
        $this->expectException(NotFoundHttpException::class);

        $this->generator->fetch('root')
            ->shouldBeCalledTimes(1)
            ->willReturn(null);

        $this->controller()->indexAction();
    }

    public function testSectionSuccessful(): void
    {
        /** @var Urlset|ObjectProphecy $urlset */
        $urlset = $this->prophesize(Urlset::class);
        $urlset->toXml()
            ->shouldBeCalledTimes(1)
            ->willReturn('<urlset/>');

        $this->generator->fetch('default')
            ->shouldBeCalledTimes(1)
            ->willReturn($urlset->reveal());

        $response = $this->controller()->sectionAction('default');
        self::assertSitemapResponse($response, '<urlset/>');
    }

    public function testSectionNotFound(): void
    {
        $this->expectException(NotFoundHttpException::class);

        $this->generator->fetch('void')
            ->shouldBeCalledTimes(1)
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
        return new SitemapController($this->generator->reveal(), self::TTL);
    }
}
