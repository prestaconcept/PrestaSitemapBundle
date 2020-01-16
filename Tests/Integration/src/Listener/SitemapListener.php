<?php

namespace Presta\SitemapBundle\Tests\Integration\Listener;

use Presta\SitemapBundle\Event\SitemapPopulateEvent;
use Presta\SitemapBundle\Service\UrlContainerInterface;
use Presta\SitemapBundle\Sitemap\Url\GoogleImage;
use Presta\SitemapBundle\Sitemap\Url\GoogleImageUrlDecorator;
use Presta\SitemapBundle\Sitemap\Url\GoogleVideoUrlDecorator;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

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

    private $routing;

    public function __construct(UrlGeneratorInterface $routing)
    {
        $this->routing = $routing;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            SitemapPopulateEvent::ON_SITEMAP_POPULATE => 'populate',
        ];
    }

    public function populate(SitemapPopulateEvent $event): void
    {
        $this->blog($event->getUrlContainer());
    }

    private function blog(UrlContainerInterface $sitemap): void
    {
        $sitemap->addUrl(
            new UrlConcrete($this->routing->generate('blog_read', [], UrlGeneratorInterface::ABSOLUTE_URL)),
            'blog'
        );

        foreach (self::BLOG as $post) {
            $url = new UrlConcrete(
                $this->routing->generate(
                    'blog_post',
                    ['slug' => $post['slug']],
                    RouterInterface::ABSOLUTE_URL
                )
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
                $parameters = parse_str($post['video']);
                $url = new GoogleVideoUrlDecorator(
                    $url,
                    sprintf('https://img.youtube.com/vi/%s/0.jpg', $parameters['v']),
                    $post['title'],
                    $post['title'],
                    ['content_loc' => $post['video']]
                );
            }

            $sitemap->addUrl($url, 'blog');
        }
    }
}
