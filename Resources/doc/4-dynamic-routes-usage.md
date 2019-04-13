# Dynamic routes usage


You can also register event listeners (or subscribers) to populate your sitemap(s).

Imagine that your application is (or has) a blog, and that you want to add to your sitemap
all blog posts that your administrator has created.

> **Note:** We choose an `event subscriber` as example, but you can also do it with an `event listener`.

If you are not familiar with the concept of event listener/subscriber/dispatcher, 
please have a look to Symfony's [official documentation](http://symfony.com/doc/current/event_dispatcher.html).


## EventListener class

```php
<?php

namespace App\EventListener;

use App\Repository\BlogPostRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Presta\SitemapBundle\Event\SitemapPopulateEvent;
use Presta\SitemapBundle\Service\UrlContainerInterface;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;

class SitemapSubscriber implements EventSubscriberInterface
{
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var BlogPostRepository
     */
    private $blogPostRepository;

    /**
     * @param UrlGeneratorInterface $urlGenerator
     * @param BlogPostRepository    $blogPostRepository
     */
    public function __construct(UrlGeneratorInterface $urlGenerator, BlogPostRepository $blogPostRepository)
    {
        $this->urlGenerator = $urlGenerator;
        $this->blogPostRepository = $blogPostRepository;
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [
            SitemapPopulateEvent::ON_SITEMAP_POPULATE => 'populate',
        ];
    }

    /**
     * @param SitemapPopulateEvent $event
     */
    public function populate(SitemapPopulateEvent $event): void
    {
        $this->registerBlogPostsUrls($event->getUrlContainer());
    }

    /**
     * @param UrlContainerInterface $urls
     */
    public function registerBlogPostsUrls(UrlContainerInterface $urls): void
    {
        $posts = $this->blogPostRepository->findAll();

        foreach ($posts as $post) {
            $urls->addUrl(
                new UrlConcrete(
                    $this->urlGenerator->generate(
                        'blog_post',
                        ['slug' => $post->getSlug()],
                        UrlGeneratorInterface::ABSOLUTE_URL
                    )
                ),
                'blog'
            );
        }
    }
}
```

> **Note:** you should not use this snippet as is. With large dataset, `findAll` is not a good idea. 
>            Please read Doctrine documentation, to learn about iterator and array hydrate.


## Service definition

If you are using PSR4 service discovery, your event listener is already registered.
Otherwhise you will have to register it by hand.


**Using XML**

```xml
<services>
    <service id="app.sitemap.blog_post_subscriber" class="App\EventListener\SitemapSubscriber">
        <argument type="service" id="router"/>
        <argument type="service" id="<your repository service id>"/>
        <tag name="kernel.event_subscriber" priority="100"/>
    </service>
</services>
```

**Using YAML**

```yaml
services:
    app.sitemap.blog_post_subscriber:
        class: App\EventListener\SitemapSubscriber
        arguments: 
            - "@router"
            - "@<your repository service id>"
        tags:
            - { name: "kernel.event_subscriber", priority: 100 }
```

> **Note:** Choosing a priority for your event listener is up to you.


---

« [Static routes usage](3-static-routes-usage.md) • [Decorating URLs](5-decorating-urls.md) »
