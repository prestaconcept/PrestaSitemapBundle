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
class GoogleMultilangUrlDecoratorTest extends \PHPUnit_Framework_TestCase
{
    public function testAddLink()
    {
        $url = new Sitemap\Url\GoogleMultilangUrlDecorator(new Sitemap\Url\UrlConcrete('http://acme.com'));

        $url->addLink('http://fr.acme.com/', 'fr');

        $xml = $url->toXml();

        $this->assertTrue(
            '<url><loc>http://acme.com</loc><xhtml:link rel="alternate" hreflang="fr" href="http://fr.acme.com/" /></url>' ==
            $xml
        );
    }
}
