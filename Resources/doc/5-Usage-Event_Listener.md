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

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Presta\SitemapBundle\Event\SitemapPopulateEvent;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;

class SitemapBlogPostSubscriber implements EventSubscriberInterface
{
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var ObjectManager
     */
    private $manager;

    /**
     * @param UrlGeneratorInterface $urlGenerator
     * @param ObjectManager         $manager
     */
    public function __construct(UrlGeneratorInterface $urlGenerator, ObjectManager $manager)
    {
        $this->urlGenerator = $urlGenerator;
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

**note :** you may not use this snippet as is. With large dataset, `findAll` is not a good idead. 
           Please read Doctrine documentation, to learn about iterator and array hydrate.


## Service configuration

**XML**

Service registering example `app/config/services.xml`

```xml
<services>
    <service id="app.sitemap.blog_post_subscriber" class="AppBundle\EventListener\SitemapBlogPostSubscriber">
        <argument type="service" id="router"/>
        <argument type="service" id="doctrine.orm.entity_manager"/>
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
        arguments: 
            - "@router"
            - "@doctrine.orm.entity_manager"
        tags:
            - { name: "kernel.event_subscriber", priority: 100 }
```

**note :** choosing a priority for your event listener is up to you.
