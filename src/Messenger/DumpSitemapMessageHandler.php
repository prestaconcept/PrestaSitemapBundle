<?php

/**
 * This file is part of the PrestaSitemapBundle package.
 *
 * (c) PrestaConcept <www.prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle\Messenger;

use Presta\SitemapBundle\Service\DumperInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Message handler to handle DumpSitemapMessage asynchronously or synchronously in background
 *
 * @author Tomas Norkūnas <norkunas.tom@gmail.com>
 */
class DumpSitemapMessageHandler implements MessageHandlerInterface
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var DumperInterface
     */
    private $dumper;

    /**
     * @var string
     */
    private $defaultTarget;

    public function __construct(RouterInterface $router, DumperInterface $dumper, string $defaultTarget)
    {
        $this->router = $router;
        $this->dumper = $dumper;
        $this->defaultTarget = $defaultTarget;
    }

    public function __invoke(DumpSitemapMessage $message)
    {
        $targetDir = rtrim($message->getTargetDir() ?? $this->defaultTarget, '/');

        if (null !== $baseUrl = $message->getBaseUrl()) {
            $baseUrl = rtrim($baseUrl, '/') . '/';

            if (!parse_url($baseUrl, PHP_URL_HOST)) {
                throw new \InvalidArgumentException(
                    'Invalid base url. Use fully qualified base url, e.g. http://acme.com/',
                    -1
                );
            }

            // Set Router's host used for generating URLs from configuration param
            // There is no other way to manage domain in CLI
            $request = Request::create($baseUrl);
            $this->router->getContext()->fromRequest($request);
        } else {
            $baseUrl = $this->getBaseUrl();
        }

        $this->dumper->dump($targetDir, $baseUrl, $message->getSection(), $message->getOptions());
    }

    private function getBaseUrl(): string
    {
        $context = $this->router->getContext();

        if ('' === $host = $context->getHost()) {
            throw new \RuntimeException(
                'Router host must be configured to be able to dump the sitemap, please see documentation.'
            );
        }

        $scheme = $context->getScheme();
        $port = '';

        if ('http' === $scheme && 80 != $context->getHttpPort()) {
            $port = ':'.$context->getHttpPort();
        } elseif ('https' === $scheme && 443 != $context->getHttpsPort()) {
            $port = ':'.$context->getHttpsPort();
        }

        return rtrim($scheme . '://' . $host . $port, '/') . '/';
    }
}
