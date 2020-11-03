# Configuration

## Changing the defaults

You may want to change the `UrlConcrete` default values:

```yaml
# config/packages/presta_sitemap.yaml
presta_sitemap:
    defaults:
        priority: 1
        changefreq: daily
        lastmod: now
```

Or choose the default sections for static routes:

```yaml
# config/packages/presta_sitemap.yaml
presta_sitemap:
    default_section: default
```


## Translated routes

If you do have some translated routes, you can configure the `alternate` section to generate alternate (hreflang) urls.

> **note** : this feature won't work if you disabled the static routes listener (see [below](#disabling-annotation-listener)).

```yaml
presta_sitemap:
    alternate:
        enabled: true
        default_locale: 'en'
        locales: ['en', 'fr']
        i18n: symfony
```

The `i18n` config value should be set accordingly to the technology you are using for your translated routes.
At the moment, this bundle supports :
- [`symfony`](https://symfony.com/doc/current/routing.html#localized-routes-i18n)
- [`jms`](http://jmsyst.com/bundles/JMSI18nRoutingBundle)

> **note** : this feature will [decorate](5-decorating-urls.md#adding-alternales) your static routes using a multilang sitemap URL.


## Time to live

You may want to change the default `3600` seconds max-age set when rendering the
sitemap. Edit the following configuration in your application.

```yaml
# config/packages/presta_sitemap.yaml
presta_sitemap:
    timetolive: 3600
```


## Configuring your application base url

If you are going to use sitemap Dumper to create sitemap files by using CLI command
you have to set the base URL of where you sitemap files will be accessible. The hostname
of the URL will also be used to make Router generate URLs with hostname.

```yaml
# config/services.yaml
parameters:
    router.request_context.host:   your-domain.com
    router.request_context.scheme: http
```

> **Note:** You may noticed that there is nothing specific to this bundle.
> In fact, doing this you just allowed your whole application to generate URLs from the command line.
> Please have a look to Symfony's [official documentation](https://symfony.com/doc/current/console/request_context.html) 
> for more information.


## Disabling annotation listener

The listener that provides annotation support is enabled by default.
To disable it, add the following configuration to your application.

```yaml
# config/packages/presta_sitemap.yaml
presta_sitemap:
   route_annotation_listener: false
```


## Items by set

You can change the default maximum number of items generated for each sitemap
with the following configuration. It cannot break the maximum limit of
50,000 items and maximum size of 1,000,000 bytes. The default value is 50,000.

```yaml
# config/packages/presta_sitemap.yaml
presta_sitemap:
    items_by_set: 50000
```


## Caching the sitemap (deprecated)

> **warning** Cache support has been deprecated since v2.3.2 and will be removed in v3.0.0.
> Please [dump](6-dumping-sitemap.md) your sitemap instead.

Sitemap can be stored in a cache.

`PrestaSitemapBundle` uses `DoctrineCacheBundle` to cache things.
You need to install the bundle and specify what kind of cache system to use with this bundle.

 * Follow the instruction to install [DoctrineCacheBundle](http://packagist.org/packages/doctrine/doctrine-cache-bundle).
 * Configure a provider for this bundle.

For example:

```yaml
# config/packages/doctrine_cache.yaml
doctrine_cache:
    providers:
        presta_sitemap:
            type: array
            namespace: presta_sitemap
```


## Changing default services

Both sitemap generator and sitemap dumper services can be changed within the configuration.

```yaml
# config/packages/presta_sitemap.yaml
presta_sitemap:
    generator: presta_sitemap.generator_default
    dumper: presta_sitemap.dumper_default
```


---

« [Installation](1-installation.md) • [Static routes usage](3-static-routes-usage.md) »
