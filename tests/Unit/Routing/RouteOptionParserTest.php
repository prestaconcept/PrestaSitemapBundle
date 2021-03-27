<?php

/*
 * This file is part of the PrestaSitemapBundle package.
 *
 * (c) PrestaConcept <https://prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle\Tests\Unit\Routing;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Presta\SitemapBundle\Routing\RouteOptionParser;
use Symfony\Component\Routing\Route;

class RouteOptionParserTest extends TestCase
{
    public function testInvalidRouteOption(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        RouteOptionParser::parse('route1', $this->getRoute('anything'));
    }

    public function testInvalidLastmodRouteOption(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        RouteOptionParser::parse('route1', $this->getRoute(['lastmod' => 'unknown']));
    }

    /**
     * @dataProvider notRegisteredOptions
     */
    public function testNotRegisteredOptions($option): void
    {
        $options = RouteOptionParser::parse('route_name', $this->getRoute($option));

        self::assertNull($options, 'Not registered to sitemap');
    }

    /**
     * @dataProvider registeredOptions
     */
    public function testRegisteredOptions(
        $option,
        ?string $section,
        ?DateTimeImmutable $lastmod,
        ?string $changefreq,
        ?float $priority
    ): void {
        $options = RouteOptionParser::parse('route_name', $this->getRoute($option));

        self::assertNotNull($options, 'Registered to sitemap');

        self::assertArrayHasKey('section', $options, '"section" option is defined');
        self::assertArrayHasKey('lastmod', $options, '"lastmod" option is defined');
        self::assertArrayHasKey('changefreq', $options, '"changefreq" option is defined');
        self::assertArrayHasKey('priority', $options, '"priority" option is defined');

        self::assertSame($section, $options['section'], '"section" option is as expected');
        self::assertEquals($lastmod, $options['lastmod'], '"lastmod" option is as expected');
        self::assertSame($changefreq, $options['changefreq'], '"changefreq" option is as expected');
        self::assertSame($priority, $options['priority'], '"priority" option is as expected');
    }

    public function notRegisteredOptions(): \Generator
    {
        yield [null];
        yield [false];
        yield ['no'];
    }

    public function registeredOptions(): \Generator
    {
        yield [true, null, null, null, null];
        yield ['yes', null, null, null, null];
        yield [['priority' => 0.5], null, null, null, 0.5];
        yield [['changefreq' => 'weekly'], null, null, 'weekly', null];
        yield [['lastmod' => '2012-01-01 00:00:00'], null, new \DateTimeImmutable('2012-01-01 00:00:00'), null, null];
        yield [['section' => 'blog'], 'blog', null, null, null];
    }

    /**
     * @param mixed $option
     *
     * @return Route
     */
    private function getRoute($option): Route
    {
        return new Route('/', [], [], ['sitemap' => $option]);
    }
}
