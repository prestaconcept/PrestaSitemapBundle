# Sitemap Events Usage

You can also register event listeners (or subscribers) to populate your sitemap(s).

Imagine that your application is (or has) a blog, and that you want to add to your sitemap
all blog posts that your administrator has created.

**note :** we choose an `event subscriber` as example, but you can also do it with an `event listener`.


## Service configuration

Implementation example `AppBundle/EventListener/SitemapBlogPostSubscriber.php`:

```php
<?php

namespace AppBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\RouterInterface;
use Presta\SitemapBundle\Event\SitemapPopulateEvent;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;

class SitemapBlogPostSubscriber implements EventSubscriberInterface
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var EntityManager
     */
    private $manager;

    /**
     * @param RouterInterface $router
     * @param EntityManager   $manager
     */
    public function __construct(RouterInterface $router, EntityManager $manager)
    {
        $this->router = $router;
        $this->manager = $manager;
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [
            SitemapPopulateEvent::ON_SITEMAP_POPULATE => 'registerBlogPostsPages',
        ];
    }

    /**
     * @param SitemapPopulateEvent $event
     */
    public function registerBlogPostsPages(SitemapPopulateEvent $event)
    {
        $posts = $this->manager->getRepository('AppBundle:BlogPost')->findAll();

        foreach ($posts as $post) {
            $event->getUrlContainer()->addUrl(
                new UrlConcrete(
                    $this->router->generate(
                        'blog_post',
                        ['slug' => $post->getSlug()],
                        RouterInterface::ABSOLUTE_URL
                    )
                ),
                'blog'
            );
        }
    }
}
```


## Service configuration

**XML**

Service registering example `app/config/services.xml`

```xml
<services>
    <service id="app.sitemap.blog_post_subscriber" class="AppBundle\EventListener\SitemapBlogPostSubscriber">
        <argument type="service" id="router"/>
        <tag name="kernel.event_subscriber" priority="100"/>
    </service>
</services>
```

**YAML**

Service registering example `app/config/services.yml`

```yaml
services:
    app.sitemap.blog_post_subscriber:
        class:     AppBundle\EventListener\SitemapBlogPostSubscriber
        arguments: ["@router"]
        tags:
            - { name: "kernel.event_subscriber", priority: 100 }
```

**note :** choosing a priority for your event listener is up to you.
