<?php

/*
 * This file is part of the PrestaSitemapBundle package.
 *
 * (c) PrestaConcept <https://prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle\Tests\Unit\Sitemap;

use PHPUnit\Framework\TestCase;
use Presta\SitemapBundle\Sitemap\Utils;

/**
 * Description of Utils
 *
 * @author David Epely <depely@prestaconcept.net>
 */
class UtilsTest extends TestCase
{
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
