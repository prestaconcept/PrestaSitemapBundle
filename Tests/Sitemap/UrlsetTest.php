<?php 

namespace Presta\SitemapBundle\Test\Sitemap;

use Presta\SitemapBundle\Sitemap;

/**
 * @author  David Epely
 */
class UrlsetTest extends \PHPUnit_Framework_TestCase
{
    protected $urlset;
    
    public function setUp()
    {
        $this->urlset = new Sitemap\Urlset('http://acme.com/sitemap.default.xml');
    }
    
    public function testAddUrl()
    {
        try {
            $this->urlset->addUrl(new Sitemap\Url\UrlConcrete('http://acme.com/'));
        } catch (\RuntimeException $e) {
            $this->fail('An exception must not be thrown');
        }
    }
    
    public function testToXml()
    {
        $this->urlset->addUrl(new Sitemap\Url\UrlConcrete('http://acme.com/'));
        
        $xml = new \DOMDocument;
        $xml->loadXML($this->urlset->toXml());
        
        $this->assertEquals(1, $xml->getElementsByTagName('url')->length);
    }
}
