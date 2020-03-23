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
class GoogleImageUrlDecoratorTest extends TestCase
{
    public function testAddImage()
    {
        $url = new Sitemap\Url\GoogleImageUrlDecorator(new Sitemap\Url\UrlConcrete('http://acme.com'));

        $failed = false;
        try {
            $url->addImage(new Sitemap\Url\GoogleImage('http://acme.com/logo.jpg'));
        } catch (\RuntimeException $e) {
            $failed = true;
        }

        self::assertFalse($failed, 'An exception must not be thrown');
    }

    public function testIsFull()
    {
        $url = new Sitemap\Url\GoogleImageUrlDecorator(new Sitemap\Url\UrlConcrete('http://acme.com'));
        self::assertFalse($url->isFull());
    }

    public function testToXml()
    {
        $url = new Sitemap\Url\GoogleImageUrlDecorator(new Sitemap\Url\UrlConcrete('http://acme.com'));

        $xml = $url->toXml();

        self::assertXmlStringEqualsXmlString(
            '<url><loc>http://acme.com</loc></url>',
            $xml
        );
    }
}
