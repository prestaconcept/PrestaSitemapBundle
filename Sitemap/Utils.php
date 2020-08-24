<?php

/**
 * This file is part of the PrestaSitemapBundle package.
 *
 * (c) PrestaConcept <www.prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle\Sitemap;

use Presta\SitemapBundle\Exception\Exception;

/**
 * Description of Utils
 *
 * @author David Epely <depely@prestaconcept.net>
 */
class Utils
{
    /**
     * @deprecated since 2.3.0, to be removed in 3.0.0
     *
     * Verify method affiliated to given param
     *
     * @param object $object
     * @param string $name
     *
     * @return string
     */
    public static function getSetMethod($object, $name)
    {
        @trigger_error(
            sprintf('Method %s is deprecated since 2.3.0.', __METHOD__),
            E_USER_DEPRECATED
        );

        $methodName = 'set' . self::camelize($name);

        if (!method_exists($object, $methodName)) {
            throw new Exception(sprintf('The set method for parameter %s is missing', $name));
        }

        return $methodName;
    }

    /**
     * @deprecated since 2.3.0, to be removed in 3.0.0
     *
     * Verify method affiliated to given param
     *
     * @param object $object
     * @param string $name
     *
     * @return string
     * @throws Exception
     */
    public static function getGetMethod($object, $name)
    {
        @trigger_error(
            sprintf('Method %s is deprecated since 2.3.0.', __METHOD__),
            E_USER_DEPRECATED
        );

        $methodName = 'get' . self::camelize($name);

        if (!method_exists($object, $methodName)) {
            throw new Exception(sprintf('The get method for parameter %s is missing', $name));
        }

        return $methodName;
    }

    /**
     * @deprecated since 2.3.0, to be removed in 3.0.0
     *
     * Legacy alias of Utils::cdata
     *
     * @param string $string
     *
     * @return string
     */
    public static function render($string)
    {
        @trigger_error(
            sprintf('Method %s is deprecated since 2.3.0, use %s::cdata instead.', __METHOD__, __CLASS__),
            E_USER_DEPRECATED
        );

        return self::cdata($string);
    }

    /**
     * Wrap string with CDATA markup
     *
     * @param string $string
     *
     * @return string
     */
    public static function cdata($string)
    {
        return '<![CDATA[' . $string . ']]>';
    }

    /**
     * Encode string with html special chars
     *
     * @param string $string
     *
     * @return string
     */
    public static function encode($string)
    {
        return htmlspecialchars($string, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /**
     * Uppercase first letter after a space or underscore
     *
     * @param string $string
     *
     * @return string
     */
    public static function camelize($string)
    {
        @trigger_error(
            sprintf('Method %s is deprecated since 2.3.0.', __METHOD__),
            E_USER_DEPRECATED
        );

        return str_replace(' ', '', ucwords(str_replace('_', ' ', $string)));
    }
}
