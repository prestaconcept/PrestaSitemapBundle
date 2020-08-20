# Decorating URLs

The `Presta\SitemapBundle\Service\UrlContainerInterface::addUrl` method first argument accepts
an instance of `Presta\SitemapBundle\Sitemap\Url\Url`, which is an interface.

In the examples you've seen in that doc, we used only `Presta\SitemapBundle\Sitemap\Url\UrlConcrete`.
It cover the minimal requirement for a sitemap XML node.

> **Note:** This bundle is only registering `Presta\SitemapBundle\Sitemap\Url\UrlConcrete`
>           instances for the static routes you configured in your app.
>           To use the following decorators, you must register the URLs all by yourself.

However this bundle provides several implementations of this interface:

- `Presta\SitemapBundle\Sitemap\Url\GoogleImageUrlDecorator`
- `Presta\SitemapBundle\Sitemap\Url\GoogleMobileUrlDecorator`
- `Presta\SitemapBundle\Sitemap\Url\GoogleMultilangUrlDecorator`
- `Presta\SitemapBundle\Sitemap\Url\GoogleNewsUrlDecorator`
- `Presta\SitemapBundle\Sitemap\Url\GoogleVideoUrlDecorator`

All these implementations are using the [decorator pattern](https://en.wikipedia.org/wiki/Decorator_pattern).
Using this pattern you will be able to nest urls and then add some information at nesting each level.

Considering that for each of the following examples after, we are in a sitemap listener method.

> **Note:** URLs of all types (routes, assets, etc...) **must** be absolute.


## Adding images

Using the image decorator.

```php
<?php
use Presta\SitemapBundle\Service\UrlContainerInterface;
use Presta\SitemapBundle\Sitemap\Url as Sitemap;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/** @var $router UrlGeneratorInterface */
/** @var $urls UrlContainerInterface */

$url = new Sitemap\UrlConcrete($router->generate('homepage', [], UrlGeneratorInterface::ABSOLUTE_URL));
$decoratedUrl = new Sitemap\GoogleImageUrlDecorator($url);
$decoratedUrl->addImage(new Sitemap\GoogleImage('https://acme.com/assets/carousel/php.gif'));
$decoratedUrl->addImage(new Sitemap\GoogleImage('https://acme.com/assets/carousel/symfony.jpg'));
$decoratedUrl->addImage(new Sitemap\GoogleImage('https://acme.com/assets/carousel/love.png'));

$urls->addUrl($decoratedUrl, 'default');
```

```xml
<url>
    <loc>https://acme.com/</loc>
    <image:image>
        <image:loc>https://acme.com/assets/carousel/php.gif</image:loc>
    </image:image>
    <image:image>
        <image:loc>https://acme.com/assets/carousel/symfony.jpg</image:loc>
    </image:image>
    <image:image>
        <image:loc>https://acme.com/assets/carousel/love.png</image:loc>
    </image:image>
</url>
```


## Configuring an URL as a mobile resource

Using the mobile decorator.

```php
<?php
use Presta\SitemapBundle\Service\UrlContainerInterface;
use Presta\SitemapBundle\Sitemap\Url as Sitemap;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/** @var $router UrlGeneratorInterface */
/** @var $urls UrlContainerInterface */

$url = new Sitemap\UrlConcrete($router->generate('mobile_homepage', [], UrlGeneratorInterface::ABSOLUTE_URL));
$decoratedUrl = new Sitemap\GoogleMobileUrlDecorator($url);

$urls->addUrl($decoratedUrl, 'default');
```

```xml
<url>
    <loc>https://m.acme.com/</loc>
    <mobile:mobile/>
</url>
```


## Adding alternales

Using the multilang decorator.

```php
<?php
use Presta\SitemapBundle\Service\UrlContainerInterface;
use Presta\SitemapBundle\Sitemap\Url as Sitemap;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/** @var $router UrlGeneratorInterface */
/** @var $urls UrlContainerInterface */

$url = new Sitemap\UrlConcrete($router->generate('homepage', [], UrlGeneratorInterface::ABSOLUTE_URL));
$decoratedUrl = new Sitemap\GoogleMultilangUrlDecorator($url);
$decoratedUrl->addLink($router->generate('homepage_fr', [], UrlGeneratorInterface::ABSOLUTE_URL), 'fr');
$decoratedUrl->addLink($router->generate('homepage_de', [], UrlGeneratorInterface::ABSOLUTE_URL), 'de');

$urls->addUrl($decoratedUrl, 'default');
```

```xml
<url>
    <loc>https://acme.com/</loc>
    <xhtml:link rel="alternate" hreflang="fr" href="https://acme.fr/"/>
    <xhtml:link rel="alternate" hreflang="fr" href="https://acme.de/"/>
</url>
```


## Adding news

Using the news decorator.

```php
<?php
use Presta\SitemapBundle\Service\UrlContainerInterface;
use Presta\SitemapBundle\Sitemap\Url as Sitemap;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/** @var $router UrlGeneratorInterface */
/** @var $urls UrlContainerInterface */

$url = new Sitemap\UrlConcrete($router->generate('homepage', [], UrlGeneratorInterface::ABSOLUTE_URL));
$decoratedUrl = new Sitemap\GoogleNewsUrlDecorator(
    $url,
    'Symfony Sitemap',
    'fr',
    new \DateTime('2020-01-01T10:00:00+00:00'),
    'Setup sitemap with Symfony'
);

$urls->addUrl($decoratedUrl, 'default');
```

```xml
<url>
    <loc>https://acme.com/</loc>
    <news:news>
        <news:publication>
            <news:name><![CDATA[Symfony Sitemap]]></news:name>
            <news:language>fr</news:language>
        </news:publication>
        <news:publication_date>2020-01-01T10:00:00+00:00</news:publication_date>
        <news:title><![CDATA[Setup sitemap with Symfony]]></news:title>
    </news:news>
</url>
```


## Adding videos

Using the video decorator.

```php
<?php
use Presta\SitemapBundle\Service\UrlContainerInterface;
use Presta\SitemapBundle\Sitemap\Url as Sitemap;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/** @var $router UrlGeneratorInterface */
/** @var $urls UrlContainerInterface */

$url = new Sitemap\UrlConcrete($router->generate('homepage', [], UrlGeneratorInterface::ABSOLUTE_URL));
$video = new Sitemap\GoogleVideo(
    'https://img.youtube.com/vi/j6IKRxH8PTg/0.jpg',
    'How to use PrestaSitemapBundle in Symfony 2.6 [1/2]',
    'In this video you will learn how to use PrestaSitemapBundle in your Symfony 2.6 projects',
    ['content_loc' => 'https://www.youtube.com/watch?v=j6IKRxH8PTg']
);
$video->addTag('php')
    ->addTag('symfony')
    ->addTag('sitemap');
$decoratedUrl = new Sitemap\GoogleVideoUrlDecorator($url);
$decoratedUrl->addVideo($video);

$urls->addUrl($decoratedUrl, 'default');
```

```xml
<url>
    <loc>https://acme.com/</loc>
    <video:video>
        <video:thumbnail_loc>https://img.youtube.com/vi/j6IKRxH8PTg/0.jpg</video:thumbnail_loc>
        <video:title><![CDATA[How to use PrestaSitemapBundle in Symfony 2.6 [1/2]]]></video:title>
        <video:description><![CDATA[In this video you will learn how to use PrestaSitemapBundle in your Symfony 2.6 projects]]></video:description>
        <video:content_loc>https://www.youtube.com/watch?v=j6IKRxH8PTg</video:content_loc>
        <video:tag>php</video:tag>
        <video:tag>symfony</video:tag>
        <video:tag>sitemap</video:tag>
    </video:video>
</url>
```


## Nesting

Of course, you can nest all those decorators for a single URL.

```php
<?php
use Presta\SitemapBundle\Service\UrlContainerInterface;
use Presta\SitemapBundle\Sitemap\Url as Sitemap;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/** @var $router UrlGeneratorInterface */
/** @var $urls UrlContainerInterface */

$url = new Sitemap\UrlConcrete($router->generate('homepage', [], UrlGeneratorInterface::ABSOLUTE_URL));

// 1st wrap: mobile
$url = new Sitemap\GoogleMobileUrlDecorator($url);

// 2nd wrap: images
$url = new Sitemap\GoogleImageUrlDecorator($url);
$url->addImage(new Sitemap\GoogleImage('https://acme.com/assets/carousel/php.gif'));
$url->addImage(new Sitemap\GoogleImage('https://acme.com/assets/carousel/symfony.jpg'));
$url->addImage(new Sitemap\GoogleImage('https://acme.com/assets/carousel/love.png'));

// 3rd wrap: multilang
$url = new Sitemap\GoogleMultilangUrlDecorator($url);
$url->addLink($router->generate('homepage_fr', [], UrlGeneratorInterface::ABSOLUTE_URL), 'fr');
$url->addLink($router->generate('homepage_de', [], UrlGeneratorInterface::ABSOLUTE_URL), 'de');

// 4th wrap: video
$video = new Sitemap\GoogleVideo(
    'https://img.youtube.com/vi/j6IKRxH8PTg/0.jpg',
    'How to use PrestaSitemapBundle in Symfony 2.6 [1/2]',
    'In this video you will learn how to use PrestaSitemapBundle in your Symfony 2.6 projects',
    ['content_loc' => 'https://www.youtube.com/watch?v=j6IKRxH8PTg']
);
$video->addTag('php')
    ->addTag('symfony')
    ->addTag('sitemap');
$url = new Sitemap\GoogleVideoUrlDecorator($url);
$url->addVideo($video);

$urls->addUrl($url, 'default');
```


## Limitations

The bundle takes care about limit constraints. For example, it automatically divide sections into smaller fragments.

But there is some cases for which it will just block you from doing forbidden things with exceptions.

- **Registering more than `1000` images for an URL**

Exception thrown: `Presta\SitemapBundle\Exception\GoogleImageException`

[see related documentation](https://support.google.com/webmasters/answer/178636)


- **Registering more than `32` tags for a video**

Exception thrown: `Presta\SitemapBundle\Exception\GoogleVideoTagException`

[see related documentation](https://developers.google.com/webmasters/videosearch/sitemaps)


- **Registering more than `5` stock tickers for a news**

Exception thrown: `Presta\SitemapBundle\Exception\GoogleNewsUrlException`

[see the related documentation](https://support.google.com/webmasters/answer/74288)


---

« [Dynamic routes usage](4-dynamic-routes-usage.md) • [Dumping Sitemap](6-dumping-sitemap.md) »
