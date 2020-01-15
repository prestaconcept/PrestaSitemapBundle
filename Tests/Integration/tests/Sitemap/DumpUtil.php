<?php

namespace Presta\SitemapBundle\Tests\Integration\Tests\Sitemap;

final class DumpUtil
{
    private const PUBLIC_DIR = __DIR__ . '/../../public';

    public static function index(): string
    {
        return self::PUBLIC_DIR . '/sitemap.xml';
    }

    public static function section(string $name): string
    {
        return self::PUBLIC_DIR . '/sitemap.' . $name . '.xml';
    }

    public static function sections(): array
    {
        return glob(self::section('*'));
    }

    public static function clean()
    {
        $files = array_merge(
            [self::index()],
            self::sections()
        );

        foreach (array_filter(array_map('realpath', $files)) as $file) {
            if (!@unlink($file)) {
                throw new \RuntimeException('Cannot delete file ' . $file);
            }
        }
    }
}
