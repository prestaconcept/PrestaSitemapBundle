<?php

/**
 * This file is part of the PrestaSitemapBundle package.
 *
 * (c) PrestaConcept <www.prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle\Tests\Unit\Sitemap;

use PHPUnit\Framework\TestCase;
use Presta\SitemapBundle\Exception\Exception;
use Presta\SitemapBundle\Sitemap\Utils;

/**
 * Description of Utils
 *
 * @author David Epely <depely@prestaconcept.net>
 */
class UtilsTest extends TestCase
{
    /**
     * @group legacy
     */
    public function testGetSetMethodException(): void
    {
        $this->expectException(Exception::class);

        $object = new \stdClass();
        Utils::getSetMethod($object, 'unknown');
    }

    /**
     * @group legacy
     */
    public function testGetGetMethodException(): void
    {
        $this->expectException(Exception::class);

        $object = new \stdClass();
        Utils::getGetMethod($object, 'unknown');
    }

    /**
     * @group legacy
     */
    public function testRender(): void
    {
        $actual = Utils::render('data w/ cdata section');
        self::assertEquals('<![CDATA[data w/ cdata section]]>', $actual);
    }

    public function testCdata(): void
    {
        $actual = Utils::cdata('data w/ cdata section');
        self::assertEquals('<![CDATA[data w/ cdata section]]>', $actual);
    }

    public function testEncode(): void
    {
        $actual = Utils::encode('data & spécial chars>');
        self::assertEquals('data &amp; spécial chars&gt;', $actual);
    }
}
