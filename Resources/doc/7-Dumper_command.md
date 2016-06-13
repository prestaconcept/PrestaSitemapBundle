# Dumper command

If you want to dump your sitemaps to files and serve them statically (like assets are served)
you can use `presta:sitemap:dump` console command. This can also be useful if you have really large sitemaps.
The command dumps them into files w/o consuming much memory.

To use it you have to set `dumper_base_url` in your config.yml (see above).
The command accepts single argument which is the folder where to dump sitemaps to, it defaults to `web`, since
most of the people keep the sitemaps in the root of their sites.
The command always creates `sitemap.xml` file as sitemaps index. The other files are named according to section names
you provide, when adding URLs in your `SitemapPopulateEvent` event listeners.

```bash
$ app/console presta:sitemap:dump
Dumping all sections of sitemaps into web directory
Created the following sitemap files
    main.xml
    main_0.xml
    sitemap.xml
```

The command first creates all sitemap files in a temporary location. Once all of the files are created
it deletes matching (by section names) files from your target directory and copies newly prepared files in place.
This happens in almost atomic way. In case anything went wrong during sitemap generation your existing sitemap files
will be untouched.

Dumper command can also be used to regenerate just a part of sitemaps (by section name). In order to do that
you have to supply `--section=name` option to the command. It will regenerate only sections with that name
and update corresponding part of sitemap index file, leaving other sitemap references intact.

To make use of these feature your Event listeners should check `$event->getSection()` in the following way:

```php
if (is_null($event->getSection()) || $event->getSection() == 'mysection') {
    $event->getUrlContainer()->addUrl(
        new UrlConcrete(
            $url,
            new \DateTime(),
            UrlConcrete::CHANGEFREQ_HOURLY,
            1
        ),
        'mysection'
    );
}
```

You can overwrite default host specified `dumper_base_url` parameter if you need to generate several sitemaps with different hosts. Consider following example:

```bash
$ app/console presta:sitemap:dump --base-url=http://es.mysite.com/ es/
Dumping all sections of sitemaps into web directory
Created the following sitemap files
    main.xml
    main_0.xml
    sitemap.xml
```

The dumper command support gzip compression as described in [sitemaps protocol][1] :

```bash
$ app/console presta:sitemap:dump --gzip
Dumping all sections of sitemaps into tmp4 directory
Created/Updated the following sitemap files:
    sitemap.default.xml.gz
    sitemap.image.xml.gz
    [...]
    sitemap.xml
```

[1]: http://www.sitemaps.org/protocol.html#index
