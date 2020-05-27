<?php

namespace Presta\SitemapBundle\Tests\Integration\Tests;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\EventListener\StopWorkerOnMessageLimitListener;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Transport\InMemoryTransport;
use Symfony\Component\Messenger\Worker;

class MessengerTest extends SitemapTestCase
{
    private const PUBLIC_DIR = __DIR__ . '/../public';
    private const GET = Request::METHOD_GET;

    protected function setUp(): void
    {
        if (!interface_exists(MessageBusInterface::class)) {
            $this->markTestSkipped('Skipping messenger tests, because it is not installed.');

            return;
        }

        foreach (glob(self::PUBLIC_DIR . '/sitemap.*') as $file) {
            if (!@unlink($file)) {
                throw new \RuntimeException('Cannot delete file ' . $file);
            }
        }
    }

    /**
     * @dataProvider gzip
     */
    public function testDumpSitemapUsingMessenger(bool $gzip): void
    {
        $kernel = self::bootKernel();

        $index = $this->index();
        self::assertFileNotExists($index, 'Sitemap index file does not exists before dump');

        $static = $this->section('static', $gzip);
        self::assertFileNotExists($static, 'Sitemap "static" section file does not exists before dump');

        $blog = $this->section('blog', $gzip);
        self::assertFileNotExists($blog, 'Sitemap "blog" section file does not exists before dump');

        $archives = $this->section('archives', $gzip);
        $archives0 = $this->section('archives_0', $gzip);
        self::assertFileNotExists($archives, 'Sitemap "archive" section file does not exists before dump');
        self::assertFileNotExists($archives0, 'Sitemap "archive_0" section file does not exists before dump');

        /** @var MessageBusInterface $messageBus */
        $messageBus = self::$container->get('messenger.default_bus');
        /** @var InMemoryTransport $transport */
        $transport = self::$container->get('messenger.transport.async');
        /** @var EventDispatcherInterface $eventDispatcher */
        $eventDispatcher = self::$container->get(EventDispatcherInterface::class);
        $eventDispatcher->addSubscriber(new StopWorkerOnMessageLimitListener(1));
        /** @var LoggerInterface $logger */
        $logger = self::$container->get(LoggerInterface::class);
        $worker = new Worker([$transport], $messageBus, $eventDispatcher, $logger);

        /** @var KernelBrowser $web */
        $web = $kernel->getContainer()->get('test.client');
        $web->request(self::GET, '/dispatch-message?gzip='.$gzip);

        $worker->run();

        // get sitemap index content via filesystem
        self::assertFileExists($index, 'Sitemap index file exists after dump');
        self::assertIsReadable($index, 'Sitemap index section file is readable');
        self::assertIndex(file_get_contents($index), $gzip);

        // get sitemap "static" section content via filesystem
        self::assertFileExists($static, 'Sitemap "static" section file exists after dump');
        self::assertIsReadable($static, 'Sitemap "static" section file is readable');
        self::assertStaticSection($this->fileContent($static, $gzip));

        // get sitemap "blog" section content via filesystem
        self::assertFileExists($blog, 'Sitemap "blog" section file exists after dump');
        self::assertIsReadable($blog, 'Sitemap "blog" section file is readable');
        self::assertBlogSection($this->fileContent($blog, $gzip));

        // get sitemap "archives" section content via filesystem
        self::assertFileExists($archives, 'Sitemap "archives" section file exists after dump');
        self::assertIsReadable($archives, 'Sitemap "archives" section file is readable');
        self::assertFileExists($archives0, 'Sitemap "archives_0" section file exists after dump');
        self::assertIsReadable($archives0, 'Sitemap "archives_0" section file is readable');
        self::assertArchivesSection($this->fileContent($archives, $gzip));
        self::assertArchivesSection($this->fileContent($archives0, $gzip));
    }

    public function gzip(): array
    {
        return [
            [false],
            [true],
        ];
    }

    private function index(): string
    {
        return self::PUBLIC_DIR . '/sitemap.xml';
    }

    private function section(string $name, bool $gzip = false): string
    {
        return self::PUBLIC_DIR . '/' . $this->sectionFile($name, $gzip);
    }

    private function sectionFile(string $name, bool $gzip = false): string
    {
        return 'sitemap.' . $name . '.xml' . ($gzip ? '.gz' : '');
    }

    private function fileContent(string $file, bool $gzip = false): string
    {
        if ($gzip === false) {
            return file_get_contents($file);
        }

        $resource = @gzopen($file, 'rb', false);
        if (!$resource) {
            throw new \RuntimeException();
        }

        $data = '';
        while (!gzeof($resource)) {
            $data .= gzread($resource, 1024);
        }
        gzclose($resource);

        return $data;
    }
}
