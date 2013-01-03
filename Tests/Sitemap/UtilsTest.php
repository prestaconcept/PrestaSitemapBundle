<?php
namespace Presta\SitemapBundle\Test\Sitemap;

use Presta\SitemapBundle\Sitemap\Utils;
use Presta\SitemapBundle\Exception\Exception;

/**
 * Description of Utils
 *
 * @author David Epely <depely@prestaconcept.net>
 */
class UtilsTest extends \PHPUnit_Framework_TestCase
{
    
    /**
     * @expectedException Exception
     */
    public function testGetSetMethodException()
    {
        $object = new \stdClass();
        Utils::getSetMethod($object, 'unknown');
    }
    
    /**
     * @expectedException Exception
     */
    public function testGetGetMethodException()
    {
        $object = new \stdClass();
        Utils::getGetMethod($object, 'unknown');
    }
    
    
    public function testRender()
    {
        $actual = Utils::render('data w/ cdata section');
        $this->assertEquals('<![CDATA[data w/ cdata section]]>', $actual);
    }
    
    public function testEncode()
    {
        $actual = Utils::encode('data & spécial chars>');
        $this->assertEquals('data &amp; spécial chars&gt;', $actual);
    }
    
    
    public function testCamelize()
    {
        $actual = Utils::camelize('data to_camelize');
        $this->assertEquals('DataToCamelize', $actual);
    }
}