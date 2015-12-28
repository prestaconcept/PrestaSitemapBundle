# Usage Quick and dirty

The only thing required is to register a url for each available page.

You need to add one or more listeners in your application that provides your
urls to PrestaSitemapBundle when called.

For example in your AcmeDemoBundle :

```php
<?php
namespace Acme\DemoBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use Presta\SitemapBundle\Event\SitemapPopulateEvent;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;

class AcmeDemoBundle extends Bundle
{
    public function boot()
    {
        $router = $this->container->get('router');
        $event  = $this->container->get('event_dispatcher');

        //listen presta_sitemap.populate event
        $event->addListener(
            SitemapPopulateEvent::ON_SITEMAP_POPULATE,
            function(SitemapPopulateEvent $event) use ($router){
                //get absolute homepage url
                $url = $router->generate('homepage', array(), UrlGeneratorInterface::ABSOLUTE_URL);

                //add homepage url to the urlset named default
                $event->getGenerator()->addUrl(
                    new UrlConcrete(
                        $url,
                        new \DateTime(),
                        UrlConcrete::CHANGEFREQ_HOURLY,
                        1
                    ),
                    'default'
                );
        });
    }
}
```

Then the sitemap can be generated and optionnaly set in cache;
the sitemapindex will be : http://acme.com/sitemap.xml
So the default section will be available at http://acme.com/sitemap.default.xml .
Note that if one limit is exceeded a new section will be added
(eg. http://acme.com/sitemap.default_1.xml)
