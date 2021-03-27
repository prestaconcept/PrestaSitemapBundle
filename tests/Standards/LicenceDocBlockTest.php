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
use SplFileInfo;
use Symfony\Component\Finder\Finder;

/**
 * Assert that all PHP files contains same LICENCE comment docblock.
 */
final class LicenceDocBlockTest extends TestCase
{
    private const EXPECTED = <<<PHP
/*
 * This file is part of the PrestaSitemapBundle package.
 *
 * (c) PrestaConcept <https://prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
PHP;

    public function testSources(): void
    {
        self::assertFilesDocBlocks(
            Finder::create()
                ->in(__DIR__ . '/../../src/')
                ->files()
                ->name('*.php')
        );
    }

    public function testTests(): void
    {
        self::assertFilesDocBlocks(
            Finder::create()
                ->in(__DIR__ . '/../../tests/')
                ->exclude('Integration/var/')
                ->files()
                ->name('*.php')
        );
    }

    private static function assertFilesDocBlocks(iterable $files): void
    {
        /** @var SplFileInfo $file */
        foreach ($files as $file) {
            $lines = \array_slice(\file($file->getPathname()), 2, 8);
            $lines = \trim(\implode('', $lines));
            self::assertSame(self::EXPECTED, $lines, "File {$file->getPathname()} contains expected LICENCE docblock");
        }
    }
}
