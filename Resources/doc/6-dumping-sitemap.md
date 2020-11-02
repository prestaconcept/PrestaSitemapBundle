# Dumping the sitemap

Back to the [installation](1-installation.md) instructions, you may have noticed that this bundle is declaring routes:
`/sitemap.xml` and `/sitemap.{section}.xml`.

That means that, whenever the sitemap is requested, it is built on demand.

For small sites, it is fast enough to be a good option.
But as your site grows (and so your sitemap), this option starts being a very bad one.

So, there is another option: saving your sitemap as an XML file in the public directory, 
so your HTTP server will serve it directly without asking the app to build it.

This is called a sitemap **dump**. 

> **Important note:** For this method to work, 
> you will have to configure your router to be able to generate absolute URL from the command line.
> Have a look to the [configuration](2-configuration.md).


## Command usage

Command accepts a single argument which is the folder where to dump sitemaps to.
It defaults to `public`, since most of the people keep the sitemaps in the root of their sites.

The command always creates `sitemap.xml` file as sitemaps index.
Other files are named according to section names you registered.

```bash
$ bin/console presta:sitemaps:dump
Dumping all sections of sitemaps into public directory
Created the following sitemap files
    main.xml
    main_0.xml
    sitemap.xml
```

> **Note:** Default directory can also be configured in the bundle configuration.
> ```yaml
>  # config/packages/presta_sitemap.yaml
> presta_sitemap:
>     dump_directory: some/dir
> ```



## What happened?

Command first creates all sitemap files in a temporary location.
Once all of the files are created, it deletes matching (by section names) files from your target directory 
and copies newly prepared files in place.
This happens in almost atomic way. 
In case anything went wrong during sitemap generation your existing sitemap files will be untouched.


## Dumping a single section

Dumper command can also be used to regenerate just a part of the sitemap.

In order to do that you have to supply `--section=name` option to the command.
It will regenerate only sections with that name and update corresponding part of sitemap index file, 
leaving other sitemap references intact.

If you wish to use this feature, you **must** wrap all your custom url registering 
with a condition about the section being dumped.

For example:

```php
<?php

use Presta\SitemapBundle\Event\SitemapPopulateEvent;
use Presta\SitemapBundle\Sitemap\Url as Sitemap;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/** @var SitemapPopulateEvent $event */
/** @var UrlGeneratorInterface $urlGenerator */

if (in_array($event->getSection(), [null, 'mysection'], true)) {
    $event->getUrlContainer()->addUrl(
        new Sitemap\UrlConcrete($urlGenerator->generate('route_in_my_section', [], UrlGeneratorInterface::ABSOLUTE_URL)),
        'mysection'
    );
}
```


## Forcing the base url

You can override Symfony's routing context host if you need to generate several sitemaps with different hosts.

For example:

```bash
$ bin/console presta:sitemaps:dump public/sitemap/es/ --base-url=http://es.mysite.com/
Dumping all sections of sitemaps into public/sitemap/es/ directory
Created the following sitemap files
    main.xml
    main_0.xml
    sitemap.xml
```


## Compressing the sitemap files

The command supports `gzip` compression:

```bash
$ bin/console presta:sitemaps:dump --gzip
Dumping all sections of sitemaps into public directory
Created/Updated the following sitemap files:
    sitemap.default.xml.gz
    sitemap.image.xml.gz
    [...]
    sitemap.xml
```

See more about compression in [sitemaps protocol](https://www.sitemaps.org/protocol.html#index).


---

+ « [Decorating URLs](5-decorating-urls.md) • [Messenger integration](7-messenger-integration.md) »
