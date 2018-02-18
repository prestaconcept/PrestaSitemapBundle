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

## Time to live

You may want to change the default `3600` seconds max-age set when rendering the
sitemap. Edit the following configuration in your application.

```yaml
# config/packages/presta_sitemap.yaml
presta_sitemap:
    timetolive: 3600
```

Also this value is used by the cache if you have installed and configured doctrine_cache.


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


## Caching the sitemap

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
