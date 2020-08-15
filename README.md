# PrestaSitemapBundle

[![Tests](https://img.shields.io/github/workflow/status/prestaconcept/PrestaSitemapBundle/Tests?style=flat-square&label=tests)](https://github.com/prestaconcept/PrestaSitemapBundle/actions)
[![Coverage](https://img.shields.io/codecov/c/github/prestaconcept/PrestaSitemapBundle?style=flat-square)](https://codecov.io/gh/prestaconcept/PrestaSitemapBundle)
[![Latest Stable Version](https://img.shields.io/packagist/v/presta/sitemap-bundle?style=flat-square)](https://packagist.org/packages/presta/sitemap-bundle)
[![Downloads Monthly](https://img.shields.io/packagist/dm/presta/sitemap-bundle?style=flat-square)](https://packagist.org/packages/presta/sitemap-bundle)
[![Contributors](https://img.shields.io/github/contributors/prestaconcept/PrestaSitemapBundle?style=flat-square)](https://github.com/prestaconcept/PrestaSitemapBundle/graphs/contributors)


PrestaSitemapBundle is a Symfony XML sitemap generator.


## Overview

A sandbox is available in a dedicated [GitHub repository](https://github.com/yann-eugone/presta-sitemap-test-project).

You may also have a look to [Prestaconcept's website sitemap](https://www.prestaconcept.net/sitemap.xml) 
(which is built with this bundle).


## Versions

This bundle is compatible with all Symfony versions since `2.3.0`.

However, like Symfony, we do not provide support for Symfony's version that reached EOL.


## Features

 * Sitemapindex
 * Google images, video, mobile and multilang urls
 * Respect constraints (50k items / 10MB per file)
 * No database required
 * Optional caching (using `DoctrineCacheBundle`)


## Documentation

You will find the detailed documentation in the following links:

* [Installation](Resources/doc/1-installation.md)
* [Configuration](Resources/doc/2-configuration.md)
* [Static routes usage](Resources/doc/3-static-routes-usage.md)
* [Dynamic routes usage](Resources/doc/4-dynamic-routes-usage.md)
* [Decorating URLs](Resources/doc/5-decorating-urls.md)
* [Dumping sitemap](Resources/doc/6-dumping-sitemap.md)
* [Messenger integration](Resources/doc/7-messenger-integration.md)


## Contributing

Please feel free to open an [issue](https://github.com/prestaconcept/PrestaSitemapBundle/issues) 
or a [pull request](https://github.com/prestaconcept/PrestaSitemapBundle), 
if you want to help.

Thanks to
[everyone who has contributed](https://github.com/prestaconcept/PrestaSitemapBundle/graphs/contributors) already.

---

*This project is supported by [PrestaConcept](http://www.prestaconcept.net)*

Released under the [MIT License](LICENSE)
