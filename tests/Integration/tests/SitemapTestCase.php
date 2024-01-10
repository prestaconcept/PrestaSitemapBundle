<?php

/*
 * This file is part of the PrestaSitemapBundle package.
 *
 * (c) PrestaConcept <https://prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle\Tests\Integration\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;

if (PHP_VERSION_ID < 80200) {
    abstract class SitemapTestCase extends BaseSitemapTestCase
    {
        protected static function getContainer(): ContainerInterface
        {
            if (\method_exists(KernelTestCase::class, 'getContainer')) {
                return parent::getContainer();
            }

            return self::$container;
        }
    }
} else {
    abstract class SitemapTestCase extends BaseSitemapTestCase
    {
        protected static function getContainer(): Container
        {
            return parent::getContainer();
        }
    }
}
