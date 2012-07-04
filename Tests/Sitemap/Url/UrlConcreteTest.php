<?php 

namespace Presta\SitemapBundle\Test\Sitemap\Url;

use Presta\SitemapBundle\Sitemap;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;

/**
 * @author  David Epely
 */
class UrlConcreteTest extends \PHPUnit_Framework_TestCase
{
    protected  $url;
            
    protected function setUp()
    {
        $this->url = new UrlConcrete('http://acme.com/');
    }
    
    public function testToXml()
    {
        $xml = new \DOMDocument;
        
        $str = '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"';
        $str .= '>' . $this->url->toXml() . '</urlset>';
        
        $xml->loadXML($str);
        
        
        $this->assertEquals(1, $xml->getElementsByTagName('url')->length);
    }
    
    
}
