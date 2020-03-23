<?php

/**
 * This file is part of the PrestaSitemapBundle package.
 *
 * (c) PrestaConcept <www.prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle\Tests\Unit\Sitemap\Url;

use PHPUnit\Framework\TestCase;
use Presta\SitemapBundle\Sitemap;

/**
 * @author David Epely <depely@prestaconcept.net>
 */
class GoogleMobileUrlDecoratorTest extends TestCase
{
    public function testToXml()
    {
        $url = new Sitemap\Url\GoogleMobileUrlDecorator(new Sitemap\Url\UrlConcrete('http://m.acme.com'));

        $xml = $url->toXml();

        self::assertTrue(
            '<url><loc>http://m.acme.com</loc><mobile:mobile/></url>' == $xml
        );
    }
}
