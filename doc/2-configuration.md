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

### Omitting `changefreq` and `priority`

Any of these may be set to `null`, in which case the tag is not written at all:

```yaml
# config/packages/presta_sitemap.yaml
presta_sitemap:
    defaults:
        priority: null
        changefreq: null
        lastmod: null
```

This is worth knowing about, because the bundle's own defaults (`priority: 0.5`,
`changefreq: daily`) are applied to **every** URL that does not set its own — so unless you
opt out, every entry in your sitemap carries both tags.

Both are still valid [sitemaps.org](https://www.sitemaps.org/protocol.html) tags, but Google
[states plainly](https://developers.google.com/search/docs/crawling-indexing/sitemaps/build-sitemap)
that it *"ignores `<priority>` and `<changefreq>` values"*. On a small sitemap that is harmless.
On a large one it is not free: the two tags add roughly 40 bytes per URL, so a 50,000-URL sitemap
carries about 2 MB of markup no consumer reads — which matters because the protocol caps a single
sitemap at 50 MB uncompressed, and that budget is better spent on URLs.

`lastmod` is a different case and generally worth keeping — but note that the default value,
`now`, means "modified at dump time" for every URL, which is exactly the kind of unverifiable
timestamp Google says it will disregard. If you cannot supply a real per-URL modification date,
setting it to `null` is more honest than stamping every URL with the time the sitemap was built.

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
# config/packages/routing.yaml
framework:
    router:
        default_uri: 'https://your-domain.com'
```

> **Note:** You may have noticed that there is nothing specific to this bundle.
> In fact, doing this you just allowed your whole application to generate URLs from the command line.
> Please have a look to Symfony's [official documentation](https://symfony.com/doc/current/routing.html#generating-urls-in-commands) 
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
