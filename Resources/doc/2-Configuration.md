# Configuration

## Time to live

You may want to change the default 3600 seconds max-age set when rendering the
sitemap. Edit the following configuration in your application.

```yaml
presta_sitemap:
    timetolive: 3600
```

Also this value is used by the cache if you have installed and configured doctrine_cache.

## The base URL for dumper

If you are going to use sitemap Dumper to create sitemap files by using CLI command
you have to set the base URL of where you sitemap files will be accessible. The hostname
of the URL will also be used to make Router generate URLs with hostname.

```yaml
# app/config/parameters.yml
parameters:
    router.request_context.host:   your-domain.com
    router.request_context.scheme: http
```


## Annotation

The listener that provides annotation support is enabled by default. To disable it, add the following configuration to
your application.

```yaml
presta_sitemap:
   route_annotation_listener: false
```

## Items by set [optional]

You can change the default maximum number of items generated for each sitemap
with the following configuration. It cannot break the maximum limit of
50,000 items and maximum size of 1,000,000 bytes. The default value is 50,000.

```yaml
presta_sitemap:
    items_by_set: 50000
```

## Cache [optional]

Each sitemap can be stored in your cache system:

PrestaSitemapBundle uses Symfony Cache component to store Cache. This component
provides an extended PSR-6 implementation as well as a PSR-16 "Simple Cache" implementation
with ready to use adapters for the most common caching backends. You need to install
Symfony Cache and specify what pool cache to use with PrestaSitemap.

 * Follow the instructions to install [Symfony Cache](https://symfony.com/doc/current/components/cache.html#installation).
 * Configure a Symfony Cache pool for PrestaSitemap.
   Symfony Cache comes with a predefined cache pool named `cache.app`.
   This is an example in `app/config/config.yml` with it:

```yaml
presta_sitemap:
    cache:
        pool: cache.app
```

You can also specify a time to live and a namespace for its elements like this:

```yaml
presta_sitemap:
    cache:
        pool: cache.app
        timetolive: 3600
        namespace: presta_sitemap
```
