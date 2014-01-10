<?php

/**
 * This file is part of the PrestaSitemapBundle
 *
 * (c) PrestaConcept <www.prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle\Sitemap;

use Presta\SitemapBundle\Exception\Exception;

if (!defined('ENT_SUBSTITUTE')) {
    define('ENT_SUBSTITUTE', 8);
}

/**
 * Description of Utils
 *
 * @author David Epely <depely@prestaconcept.net>
 */
class Utils
{
    /**
     * verify method affiliated to given param
     *
     * @param string $name
     * @return string
     * @throws Exception
     */
    public static function getSetMethod($object, $name)
    {
        $methodName = 'set' . self::camelize($name);

        if (!method_exists($object, $methodName)) {
            throw new Exception(sprintf('The set method for parameter %s is missing', $name));
        }

        return $methodName;
    }

    /**
     * verify method affiliated to given param
     *
     * @param string $name
     * @return string
     * @throws Exception
     */
    public static function getGetMethod($object, $name)
    {
        $methodName = 'get' . self::camelize($name);

        if (!method_exists($object, $methodName)) {
            throw new Exception(sprintf('The get method for parameter %s is missing', $name));
        }

        return $methodName;
    }

    /**
     * Render a string as CDATA section
     *
     * @param str $string
     * @return str
     */
    public static function render($string)
    {
        return '<![CDATA[' . $string . ']]>';
    }

    /**
     * Encode special chars
     *
     * @param type $string
     * @return type
     */
    public static function encode($string)
    {
        return htmlspecialchars($string, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /**
     * uppercase first letter after a space or underscore
     * @param type $string
     * @return type
     */
    public static function camelize($string)
    {
        return str_replace(' ', '', ucwords(str_replace('_', ' ', $string)));
    }
}
