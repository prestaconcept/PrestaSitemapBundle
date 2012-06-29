<?php 

namespace Presta\SitemapBundle\Test\Sitemap\Url;

use Presta\SitemapBundle\Sitemap;

/**
 * @author  David Epely
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
