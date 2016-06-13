# Usage Sitemap Event Listeners

You can also register your sitemap event listeners by creating service classes implementing
`Presta\SitemapBundle\Service\SitemapListenerInterface` and tagging these services with `presta.sitemap.listener`
tag in your `Resources/config/services.xml`. This way the services will be lazy-loaded by Symfony's event dispatcher, only when the event is dispatched:

```xml
<parameters>
    <parameter key="acme_demo.sitemap.listener.class">Acme\DemoBundle\EventListener\SitemapListener</parameter>
</parameters>

<services>
    <service id="my.sitemap.listener" class="%acme_demo.sitemap.listener.class%">
        <tag name="presta.sitemap.listener" />
        <argument type="service" id="router"/>
    </service>
</services>
```

or in yaml

```yaml
parameters:
    acme_demo.sitemap.listener.class: Acme\DemoBundle\EventListener\SitemapListener

services:
    my.sitemap.listener:
        class: "%acme_demo.sitemap.listener.class%"
        arguments: ["@router"]
        tags: [{name: "presta.sitemap.listener"}]
```

Sitemap listener example `Acme/DemoBundle/EventListener/SitemapListener.php`

```php
<?php
namespace Acme\DemoBundle\EventListener;

use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use Presta\SitemapBundle\Service\SitemapListenerInterface;
use Presta\SitemapBundle\Event\SitemapPopulateEvent;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;

class SitemapListener implements SitemapListenerInterface
{
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function populateSitemap(SitemapPopulateEvent $event)
    {
        $section = $event->getSection();
        if (is_null($section) || $section == 'default') {
            //get absolute homepage url
            $url = $this->router->generate('homepage', array(), UrlGeneratorInterface::ABSOLUTE_URL);

            //add homepage url to the urlset named default
            $event->getUrlContainer()->addUrl(
                new UrlConcrete(
                    $url,
                    new \DateTime(),
                    UrlConcrete::CHANGEFREQ_HOURLY,
                    1
                ),
                'default'
            );
        }
    }
}
```
