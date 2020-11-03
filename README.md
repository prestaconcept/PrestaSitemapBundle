# PrestaSitemapBundle

[![Tests](https://img.shields.io/github/workflow/status/prestaconcept/PrestaSitemapBundle/Tests?style=flat-square&label=tests)](https://github.com/prestaconcept/PrestaSitemapBundle/actions)
[![Coverage](https://img.shields.io/codecov/c/github/prestaconcept/PrestaSitemapBundle?style=flat-square)](https://codecov.io/gh/prestaconcept/PrestaSitemapBundle)
[![Latest Stable Version](https://img.shields.io/packagist/v/presta/sitemap-bundle?style=flat-square)](https://packagist.org/packages/presta/sitemap-bundle)
[![Downloads Monthly](https://img.shields.io/packagist/dm/presta/sitemap-bundle?style=flat-square)](https://packagist.org/packages/presta/sitemap-bundle)
[![Contributors](https://img.shields.io/github/contributors/prestaconcept/PrestaSitemapBundle?style=flat-square)](https://github.com/prestaconcept/PrestaSitemapBundle/graphs/contributors)


This bundle handle your XML sitemap in a Symfony application.


## Overview

Allow sitemapindex file:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<sitemapindex xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
              xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/siteindex.xsd"
              xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <sitemap>
        <loc>https://acme.org/sitemap.static.xml</loc>
        <lastmod>2020-01-01T10:00:00+02:00</lastmod>
    </sitemap>
</sitemapindex>
```

and urlset files:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" 
        xmlns:xhtml="http://www.w3.org/1999/xhtml">
    <url>
        <loc>https://acme.org/</loc>
        <lastmod>2020-01-01T10:00:00+02:00</lastmod>
        <changefreq>daily</changefreq>
        <priority>0.5</priority>
    </url>
    <url>
        <loc>https://acme.org/contact</loc>
        <lastmod>2020-01-01T10:00:00+02:00</lastmod>
        <changefreq>daily</changefreq>
        <priority>0.5</priority>
    </url>
</urlset>
```

A sandbox is available in a dedicated [GitHub repository](https://github.com/yann-eugone/presta-sitemap-test-project).

You may also have a look to [Prestaconcept's website sitemap](https://www.prestaconcept.net/sitemap.xml) 
(which is built with this bundle).


## Features

* Configure with a single option which routes you want to include in your sitemap
* Generate one Sitemapindex and as many Urlset as you need
* Access sitemap on the fly with a symfony controller or Dump sitemap to files for faster sitemap
* Comply with Urlset specifications : 50k items / 10MB per file
* Decorates your sitemap with images, video, mobile and multilang urls
* No database required


## Documentation

You will find the detailed documentation in the following links:

* [Installation](Resources/doc/1-installation.md)
* [Configuration](Resources/doc/2-configuration.md)
* [Static routes usage](Resources/doc/3-static-routes-usage.md)
* [Dynamic routes usage](Resources/doc/4-dynamic-routes-usage.md)
* [Decorating URLs](Resources/doc/5-decorating-urls.md)
* [Dumping sitemap](Resources/doc/6-dumping-sitemap.md)
* [Messenger integration](Resources/doc/7-messenger-integration.md)


## Versions

This bundle is compatible with all Symfony versions since `2.3.0`.

However, like Symfony, we do not provide support for Symfony's version that reached EOL.


## Contributing

Please feel free to open an [issue](https://github.com/prestaconcept/PrestaSitemapBundle/issues) 
or a [pull request](https://github.com/prestaconcept/PrestaSitemapBundle), 
if you want to help.

Thanks to
[everyone who has contributed](https://github.com/prestaconcept/PrestaSitemapBundle/graphs/contributors) already.

---

*This project is supported by [PrestaConcept](http://www.prestaconcept.net)*

Released under the [MIT License](LICENSE)
