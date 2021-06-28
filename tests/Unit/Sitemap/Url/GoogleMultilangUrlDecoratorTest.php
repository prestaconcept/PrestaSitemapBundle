<?php

/*
 * This file is part of the PrestaSitemapBundle package.
 *
 * (c) PrestaConcept <https://prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle\Tests\Unit\Sitemap\Url;

use PHPUnit\Framework\TestCase;
use Presta\SitemapBundle\Sitemap;

class GoogleMultilangUrlDecoratorTest extends TestCase
{
    public function testAddLink(): void
    {
        $url = new Sitemap\Url\GoogleMultilangUrlDecorator(new Sitemap\Url\UrlConcrete('http://acme.com'));

        $url->addLink('http://fr.acme.com/', 'fr');

        $xml = $url->toXml();

        self::assertTrue(
            '<url><loc>http://acme.com</loc><xhtml:link rel="alternate" hreflang="fr" href="http://fr.acme.com/" /></url>' ==
            $xml
        );
    }
}
