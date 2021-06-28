<?php

/*
 * This file is part of the PrestaSitemapBundle package.
 *
 * (c) PrestaConcept <https://prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle\Tests\Unit\Messenger;

use PHPUnit\Framework\TestCase;
use Presta\SitemapBundle\Messenger\DumpSitemapMessage;

class DumpSitemapMessageTest extends TestCase
{
    public function testConstructWithProvidedData(): void
    {
        $message = new DumpSitemapMessage('audio', 'https://acme.org', '/etc/sitemap', ['gzip' => true]);

        self::assertSame('audio', $message->getSection());
        self::assertSame('https://acme.org', $message->getBaseUrl());
        self::assertSame('/etc/sitemap', $message->getTargetDir());
        self::assertSame(['gzip' => true], $message->getOptions());
    }
}
