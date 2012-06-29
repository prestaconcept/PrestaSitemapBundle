<?php 

namespace Presta\SitemapBundle\Test\Sitemap\Url;

use Presta\SitemapBundle\Sitemap;

/**
 * @author  David Epely
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
