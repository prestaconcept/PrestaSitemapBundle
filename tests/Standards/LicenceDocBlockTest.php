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

/**
 * Assert that all PHP files contains same LICENCE comment docblock.
 */
final class LicenceDocBlockTest extends StandardsTestCase
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
        self::assertFilesDocBlocks(self::getSourceFiles());
    }

    public function testTests(): void
    {
        self::assertFilesDocBlocks(self::getTestFiles());
    }

    private static function assertFilesDocBlocks(iterable $files): void
    {
        foreach ($files as ['relative' => $relative, 'absolute' => $absolute]) {
            $lines = \array_slice(\file($absolute), 2, 8);
            $lines = \trim(\implode('', $lines));
            self::assertSame(self::EXPECTED, $lines, "File {$relative} contains expected LICENCE docblock");
        }
    }
}
