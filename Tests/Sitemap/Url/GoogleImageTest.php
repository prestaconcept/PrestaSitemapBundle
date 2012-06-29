<?php 

namespace Presta\SitemapBundle\Test\Sitemap\Url;

use Presta\SitemapBundle\Sitemap;

/**
 * @author  David Epely
 */
class GoogleImageTest extends \PHPUnit_Framework_TestCase
{
    public function testToXml()
    {
        try {
           $image = new Sitemap\Url\GoogleImage('http://acme.com/logo.jpg', 'this is about logo', 'Lyon, France', 'The Acme logo', 'WTFPL');
        } catch (\RuntimeException $e) {
            $this->fail('An exception must not be thrown');
        }
        
        $xml = $image->toXml();
        $this->assertTrue(
            '<image:image><image:loc>http://acme.com/logo.jpg</image:loc><image:caption>this is about logo</image:caption><image:geo_location>Lyon, France</image:geo_location><image:title>The Acme logo</image:title><image:license>WTFPL</image:license></image:image>' ==
            $xml
            );
        
    }
}
