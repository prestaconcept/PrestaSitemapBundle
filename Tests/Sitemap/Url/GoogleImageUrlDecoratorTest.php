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
class GoogleImageUrlDecoratorTest extends \PHPUnit_Framework_TestCase
{
    public function testAddImage()
    {
        $url = new Sitemap\Url\GoogleImageUrlDecorator(new Sitemap\Url\UrlConcrete('http://acme.com'));

        try {
            $url->addImage(new Sitemap\Url\GoogleImage('http://acme.com/logo.jpg'));
        } catch (\RuntimeException $e) {
            $this->fail('An exception must not be thrown');
        }
    }

    public function testIsFull()
    {
        $url = new Sitemap\Url\GoogleImageUrlDecorator(new Sitemap\Url\UrlConcrete('http://acme.com'));
        $this->assertFalse($url->isFull());
    }

    public function testToXml()
    {
        $url = new Sitemap\Url\GoogleImageUrlDecorator(new Sitemap\Url\UrlConcrete('http://acme.com'));

        $xml = $url->toXml();

        $this->assertXmlStringEqualsXmlString(
            '<url><loc>http://acme.com</loc></url>',
            $xml
        );
    }
}
