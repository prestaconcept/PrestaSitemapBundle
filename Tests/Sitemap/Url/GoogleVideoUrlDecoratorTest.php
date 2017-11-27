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

use Presta\SitemapBundle\Sitemap\Url\GoogleVideoUrlDecorator;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;

/**
 * @author David Epely <depely@prestaconcept.net>
 */
class GoogleVideoUrlDecoratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $xml;

    protected function setUp()
    {
        $url = new GoogleVideoUrlDecorator(
            new UrlConcrete('http://acme.com/'),
            'http://acme.com/video/thumbnail.jpg',
            'Acme video',
            'An acme video for testing purposes',
            array(
                'content_loc'   => 'http://acme.com/video/content.flv',
                'player_loc'    => 'http://acme.com/video/player.swf?a=b&c=d',
                'duration'      => '600',
                'expiration_date'   => new \DateTime,
                'rating'        => 4.2,
                'view_count'    => 42,
                'publication_date'  => new \DateTime,
                'family_friendly'   => GoogleVideoUrlDecorator::FAMILY_FRIENDLY_YES,
                'category'          => 'Testing w/ spécial chars',
                'restriction_allow' => array('FR', 'BE'),
                'restriction_deny'  => array('GB'),
                'gallery_loc'       => 'http://acme.com/video/gallery/?p=1&sort=desc',
                'gallery_loc_title' => 'Gallery for testing purposes',
                'requires_subscription' => GoogleVideoUrlDecorator::REQUIRES_SUBSCRIPTION_YES,
                'uploader'          => 'depely',
                'uploader_info'     => 'http://acme.com/video/users/1/',
                'platforms'         => array(GoogleVideoUrlDecorator::PLATFORM_WEB, GoogleVideoUrlDecorator::PLATFORM_MOBILE),
                'platform_relationship' => GoogleVideoUrlDecorator::PLATFORM_RELATIONSHIP_ALLOW,
                'live'              => GoogleVideoUrlDecorator::LIVE_NO,
            )
        );

        $url->addTag('acme');
        $url->addTag('testing');
        $url->addPrice(42, 'EUR', GoogleVideoUrlDecorator::PRICE_TYPE_OWN, GoogleVideoUrlDecorator::PRICE_RESOLUTION_HD);
        $url->addPrice(53, 'USD');

        $this->xml = new \DOMDocument;

        $xml = '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"';

        foreach ($url->getCustomNamespaces() as $name => $uri) {
            $xml .= ' xmlns:' . $name . '="' . $uri . '"';
        }

        $xml .= '>' . $url->toXml() . '</urlset>';

        $this->xml->loadXML($xml);
    }

    public function testCountNamespaces()
    {
        $namespaces = $this->xml->getElementsByTagNameNS('http://www.google.com/schemas/sitemap-video/1.1', '*');
        $this->assertEquals(24, $namespaces->length);
    }

    public function testEncodeUrl()
    {
        $playerLoc = $this->xml->getElementsByTagName('player_loc')->item(0)->nodeValue;
        $this->assertEquals($playerLoc, 'http://acme.com/video/player.swf?a=b&c=d');
    }

    public function testRenderCategory()
    {
        $category = $this->xml->getElementsByTagName('category')->item(0)->nodeValue;
        $this->assertEquals($category, 'Testing w/ spécial chars');
    }
}
