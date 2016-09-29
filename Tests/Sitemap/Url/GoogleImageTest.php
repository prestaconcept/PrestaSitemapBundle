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
class GoogleImageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider testToXmlProvider
     */
    public function testToXml($expectedXml, $loc, $caption = null, $geoLocalisation = null, $title = null, $license = null)
    {
        try {
            $image = new Sitemap\Url\GoogleImage($loc, $caption, $geoLocalisation, $title, $license);
        } catch (\RuntimeException $e) {
            $this->fail('An exception must not be thrown');
        }

        $this->assertEquals($expectedXml, $image->toXML());
    }

    public function testToXmlProvider()
    {
        return array(
            array('<image:image><image:loc>http://acme.com/logo.jpg</image:loc><image:caption><![CDATA[this is about logo]]></image:caption><image:geo_location><![CDATA[Lyon, France]]></image:geo_location><image:title><![CDATA[The Acme logo]]></image:title><image:license><![CDATA[WTFPL]]></image:license></image:image>', 'http://acme.com/logo.jpg', 'this is about logo', 'Lyon, France', 'The Acme logo', 'WTFPL'),
            array('<image:image><image:loc>http://acme.com/logo.jpg?a=&amp;b=c</image:loc><image:caption><![CDATA[this is about <strong>logo</strong>]]></image:caption></image:image>', 'http://acme.com/logo.jpg?a=&b=c', 'this is about <strong>logo</strong>'),
        );
    }
}
