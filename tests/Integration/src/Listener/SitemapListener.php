<?php

/*
 * This file is part of the PrestaSitemapBundle package.
 *
 * (c) PrestaConcept <https://prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle\Tests\Integration\Listener;

use Presta\SitemapBundle\Event\SitemapPopulateEvent;
use Presta\SitemapBundle\Service\UrlContainerInterface;
use Presta\SitemapBundle\Sitemap\Url\GoogleImage;
use Presta\SitemapBundle\Sitemap\Url\GoogleImageUrlDecorator;
use Presta\SitemapBundle\Sitemap\Url\GoogleVideo;
use Presta\SitemapBundle\Sitemap\Url\GoogleVideoUrlDecorator;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class SitemapListener implements EventSubscriberInterface
{
    private const BLOG = [
        [
            'title' => 'Post without media',
            'slug' => 'post-without-media',
            'images' => [],
            'video' => null,
        ],
        [
            'title' => 'Post with one image',
            'slug' => 'post-with-one-image',
            'images' => ['http://lorempixel.com/400/200/technics/1'],
            'video' => null,
        ],
        [
            'title' => 'Post with a video',
            'slug' => 'post-with-a-video',
            'images' => [],
            'video' => 'https://www.youtube.com/watch?v=j6IKRxH8PTg',
        ],
        [
            'title' => 'Post with multimedia',
            'slug' => 'post-with-multimedia',
            'images' => ['http://lorempixel.com/400/200/technics/2', 'http://lorempixel.com/400/200/technics/3'],
            'video' => 'https://www.youtube.com/watch?v=JugaMuswrmk',
        ],
    ];

    public static function getSubscribedEvents(): array
    {
        return [
            SitemapPopulateEvent::class => 'populate',
        ];
    }

    public function populate(SitemapPopulateEvent $event): void
    {
        if (in_array($event->getSection(), ['blog', null], true)) {
            $this->blog($event->getUrlContainer(), $event->getUrlGenerator());
        }

        if (in_array($event->getSection(), ['archives', null], true)) {
            $this->archives($event->getUrlContainer(), $event->getUrlGenerator());
        }
    }

    private function blog(UrlContainerInterface $sitemap, UrlGeneratorInterface $router): void
    {
        foreach (self::BLOG as $post) {
            $url = new UrlConcrete(
                $this->url($router, 'blog_post', ['slug' => $post['slug']])
            );

            if (count($post['images']) > 0) {
                $url = new GoogleImageUrlDecorator($url);
                foreach ($post['images'] as $idx => $image) {
                    $url->addImage(
                        new GoogleImage($image, sprintf('%s - %d', $post['title'], $idx + 1))
                    );
                }
            }

            if ($post['video'] !== null) {
                parse_str(parse_url($post['video'], PHP_URL_QUERY), $parameters);
                $url = new GoogleVideoUrlDecorator($url);
                $url->addVideo(
                    new GoogleVideo(
                        sprintf('https://img.youtube.com/vi/%s/0.jpg', $parameters['v']),
                        $post['title'],
                        $post['title'],
                        ['content_location' => $post['video']]
                    )
                );
            }

            $sitemap->addUrl($url, 'blog');
        }
    }

    private function archives(UrlContainerInterface $sitemap, UrlGeneratorInterface $router): void
    {
        $url = $this->url($router, 'archive');
        for ($i = 1; $i <= 20; $i++) {
            $sitemap->addUrl(new UrlConcrete($url . '?i=' . $i), 'archives');
        }
    }

    private function url(UrlGeneratorInterface $router, string $route, array $parameters = []): string
    {
        return $router->generate($route, $parameters, UrlGeneratorInterface::ABSOLUTE_URL);
    }
}
