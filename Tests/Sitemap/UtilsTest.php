<?php

/**
 * This file is part of the PrestaSitemapBundle package.
 *
 * (c) PrestaConcept <www.prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle\Test\Sitemap;

use PHPUnit\Framework\TestCase;
use Presta\SitemapBundle\Sitemap\Utils;
use Presta\SitemapBundle\Exception\Exception;

/**
 * Description of Utils
 *
 * @author David Epely <depely@prestaconcept.net>
 */
class UtilsTest extends TestCase
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
