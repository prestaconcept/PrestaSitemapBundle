<?php

/**
 * This file is part of the PrestaSitemapBundle package.
 *
 * (c) PrestaConcept <www.prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle\Test\Sitemap\Url;

use Presta\SitemapBundle\Sitemap;

/**
 * @author David Epely <depely@prestaconcept.net>
 */
class GoogleMobileUrlDecoratorTest extends \PHPUnit_Framework_TestCase
{
    public function testToXml()
    {
        $url = new Sitemap\Url\GoogleMobileUrlDecorator(new Sitemap\Url\UrlConcrete('http://m.acme.com'));

        $xml = $url->toXml();

        $this->assertTrue(
            '<url><loc>http://m.acme.com</loc><mobile:mobile/></url>' == $xml
        );
    }
}
