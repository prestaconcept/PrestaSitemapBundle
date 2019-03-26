# Decorating URLs

The `Presta\SitemapBundle\Service\UrlContainerInterface::addUrl` method first argument accepts 
an instance of `Presta\SitemapBundle\Sitemap\Url\Url`, which is a interface.

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


## Adding images

Using the image decorator.

```php
<?php
use Presta\SitemapBundle\Sitemap\Url as Sitemap;

/** @var $urlGenerator UrlGeneratorInterface */
$url = new Sitemap\UrlConcrete($urlGenerator->generate('homepage'));
$decoratedUrl = new Sitemap\GoogleImageUrlDecorator($url);
$decoratedUrl->addImage(new Sitemap\GoogleImage('/assets/carousel/php.gif'));
$decoratedUrl->addImage(new Sitemap\GoogleImage('/assets/carousel/symfony.jpg'));
$decoratedUrl->addImage(new Sitemap\GoogleImage('/assets/carousel/love.png'));

/** @var $urls \Presta\SitemapBundle\Service\UrlContainerInterface */
$urls->addUrl($decoratedUrl, 'default');
```


## Configuring an URL as a mobile resource

Using the mobile decorator.

```php
<?php
use Presta\SitemapBundle\Sitemap\Url as Sitemap;

/** @var $urlGenerator UrlGeneratorInterface */
$url = new Sitemap\UrlConcrete($urlGenerator->generate('mobile_homepage'));
$decoratedUrl = new Sitemap\GoogleMobileUrlDecorator($url);

/** @var $urls \Presta\SitemapBundle\Service\UrlContainerInterface */
$urls->addUrl($decoratedUrl, 'default');
```


## Adding alternales

Using the multilang decorator.

```php
<?php
use Presta\SitemapBundle\Sitemap\Url as Sitemap;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/** @var $urlGenerator UrlGeneratorInterface */
$url = new Sitemap\UrlConcrete($urlGenerator->generate('homepage'));
$decoratedUrl = new Sitemap\GoogleMultilangUrlDecorator($url);
$decoratedUrl->addLink($urlGenerator->generate('homepage_fr'), 'fr');
$decoratedUrl->addLink($urlGenerator->generate('homepage_de'), 'de');

/** @var $urls \Presta\SitemapBundle\Service\UrlContainerInterface */
$urls->addUrl($decoratedUrl, 'default');
```


## Adding news

Using the news decorator.

```php
<?php
use Presta\SitemapBundle\Sitemap\Url as Sitemap;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/** @var $urlGenerator UrlGeneratorInterface */
$url = new Sitemap\UrlConcrete($urlGenerator->generate('homepage'));
$decoratedUrl = new Sitemap\GoogleNewsUrlDecorator(
    $url,
    'PrestaSitemapBundle News',
    'fr',
    new \DateTime('2018-02-13'),
    'The docs were updated'
);

/** @var $urls \Presta\SitemapBundle\Service\UrlContainerInterface */
$urls->addUrl($decoratedUrl, 'default');
```


## Adding videos

Using the video decorator.

```php
<?php
use Presta\SitemapBundle\Sitemap\Url as Sitemap;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/** @var $urlGenerator UrlGeneratorInterface */
$url = new Sitemap\UrlConcrete($urlGenerator->generate('mobile_homepage'));
$decoratedUrl = new Sitemap\GoogleVideoUrlDecorator(
    $url,
    'https://img.youtube.com/vi/j6IKRxH8PTg/0.jpg',
    'How to use PrestaSitemapBundle in Symfony 2.6 [1/2]',
    'In this video you will learn how to use PrestaSitemapBundle in your Symfony 2.6 projects',
    ['content_loc' => 'https://www.youtube.com/watch?v=j6IKRxH8PTg']
);
$decoratedUrl->addTag('php')
    ->addTag('symfony')
    ->addTag('sitemap');

/** @var $urls \Presta\SitemapBundle\Service\UrlContainerInterface */
$urls->addUrl($decoratedUrl, 'default');
```


## Nesting

Of course, you can nest all those decorators for a single URL.

```php
<?php
use Presta\SitemapBundle\Sitemap\Url as Sitemap;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/** @var $urlGenerator UrlGeneratorInterface */
$url = new Sitemap\UrlConcrete($urlGenerator->generate('mobile_homepage', [], UrlGeneratorInterface::ABSOLUTE_URL));

// 1st wrap: mobile
$url = new Sitemap\GoogleMobileUrlDecorator($url);

// 2nd wrap: images
$url = new Sitemap\GoogleImageUrlDecorator($url);
$url->addImage(new Sitemap\GoogleImage('/assets/carousel/php.gif'));
$url->addImage(new Sitemap\GoogleImage('/assets/carousel/symfony.jpg'));
$url->addImage(new Sitemap\GoogleImage('/assets/carousel/love.png'));

// 3rd wrap: multilang
$url = new Sitemap\GoogleMultilangUrlDecorator($url);
$url->addLink($urlGenerator->generate('mobile_homepage_fr'), 'fr');
$url->addLink($urlGenerator->generate('mobile_homepage_de'), 'de');

// 4th wrap: video
$url = new Sitemap\GoogleVideoUrlDecorator(
    $url,
    'https://img.youtube.com/vi/j6IKRxH8PTg/0.jpg',
    'How to use PrestaSitemapBundle in Symfony 2.6 [1/2]',
    'In this video you will learn how to use PrestaSitemapBundle in your Symfony 2.6 projects',
    ['content_loc' => 'https://www.youtube.com/watch?v=j6IKRxH8PTg']
);
$url->addTag('php')
    ->addTag('symfony')
    ->addTag('sitemap');

/** @var $urls \Presta\SitemapBundle\Service\UrlContainerInterface */
$urls->addUrl($url, 'default');
```


## Limitations

The bundle takes care about limit constraints. For example, it automatically divide sections into smaller fragments.

But there is some cases for which it will just block you from doing forbidden things with exceptions.

- **Registering more than `1000` images for an URL**

Exception thrown: `Presta\SitemapBundle\Exception\GoogleImageException`

[see related documentation](https://support.google.com/webmasters/answer/178636)


- **Registering more than `32` tags for a video**

Exception thrown: `Presta\SitemapBundle\Exception\GoogleVideoUrlTagException`

[see related documentation](https://developers.google.com/webmasters/videosearch/sitemaps)


- **Registering more than `5` stock tickers for a news**

Exception thrown: `Presta\SitemapBundle\Exception\GoogleNewsUrlException`

[see the related documentation](https://support.google.com/webmasters/answer/74288)


---

« [Dynamic routes usage](4-dynamic-routes-usage.md) • [Dumping Sitemap](6-dumping-sitemap.md) »
