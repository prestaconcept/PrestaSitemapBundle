<?php

/**
 * This file is part of the PrestaSitemapBundle package.
 *
 * (c) PrestaConcept <www.prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

if (file_exists($file = __DIR__ . '/../vendor/autoload.php')) {
    require_once $file;
} else {
    throw new \RuntimeException('Dependencies are required');
}

spl_autoload_register(
    function ($class) {
        if (0 === strpos($class, 'Presta\\SitemapBundle\\PrestaSitemapBundle')) {
            $path = __DIR__ . '/../' . implode('/', array_slice(explode('\\', $class), 3)) . '.php';
            if (!stream_resolve_include_path($path)) {
                return false;
            }
            require_once $path;
            return true;
        }
    }
);
