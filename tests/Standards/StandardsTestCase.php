<?php

/*
 * This file is part of the PrestaSitemapBundle package.
 *
 * (c) PrestaConcept <https://prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle\Tests\Standards;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Base class of standard tests.
 * Contains logic of finding files and classes owned by bundle.
 */
abstract class StandardsTestCase extends TestCase
{
    private const COMPOSER = __DIR__ . '/../../composer.json';

    private static $sources = null;
    private static $tests = null;

    /**
     * @return array<int, array<string, string>>
     */
    protected static function getSourceFiles(): array
    {
        if (self::$sources === null) {
            self::$sources = self::get(
                Finder::create()
                    ->in(__DIR__ . '/../../src/')
                    ->files()
                    ->name('*.php'),
                'src'
            );
        }

        return self::$sources;
    }

    /**
     * @return SplFileInfo[]
     */
    protected static function getTestFiles(): array
    {
        if (self::$tests === null) {
            self::$tests = self::get(
                Finder::create()
                    ->in(__DIR__ . '/../../tests/')
                    ->exclude('Integration/var/')
                    ->files()
                    ->name('*.php'),
                'tests'
            );
        }

        return self::$tests;
    }

    private static function get(iterable $files, string $dir): array
    {
        $info = [];
        /** @var SplFileInfo $file */
        foreach ($files as $file) {
            $info[] = [
                'absolute' => $file->getPathname(),
                'relative' => $dir . '/' . $file->getRelativePathname(),
                'class' => self::class($file, $dir),
            ];
        }

        return $info;
    }

    private static function class(SplFileInfo $file, string $dir): string
    {
        $classPath = \substr($file->getRelativePathname(), 0, -4);
        $json = \json_decode(\file_get_contents(self::COMPOSER), true);
        $psr4 = \array_merge($json['autoload']['psr-4'], $json['autoload-dev']['psr-4']);
        foreach ($psr4 as $namespacePrefix => $directoryPrefix) {
            if (\strpos($dir . '/' . $file->getRelativePathname(), $directoryPrefix) !== 0) {
                continue;
            }

            return $namespacePrefix . \implode('\\', \explode(\DIRECTORY_SEPARATOR, $classPath));
        }

        throw new \LogicException("Unable to determine class of file \"{$file->getPathname()}\"");
    }
}
