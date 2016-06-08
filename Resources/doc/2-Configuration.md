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

Each sitemaps can be stored in your cache system :

PrestaSitemapBundle uses DoctrineCacheBundle to store Cache.
This bundle provides an abstract access to any Doctrine Common Cache classes.
You need to install DoctrineCacheBundle and specify what kind of cache
system to use with PrestaSitemap.

 * Follow the instruction to install [DoctrineCacheBundle](http://packagist.org/packages/doctrine/doctrine-cache-bundle).
 * Configure a service for PrestaSitemap, this is an exemple in `app/config/config.yml` with php-apc :

```yaml
doctrine_cache:
    providers:
        presta_sitemap:
            type: array #or anything your project might use (please see [DoctrineCacheBundle documentation](http://packagist.org/packages/doctrine/doctrine-cache-bundle))
            namespace: presta_sitemap
```
