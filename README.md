# PrestaSitemapBundle

[![Build Status](https://secure.travis-ci.org/prestaconcept/PrestaSitemapBundle.png)](http://travis-ci.org/prestaconcept/PrestaSitemapBundle)

[![Latest Stable Version](https://poser.pugx.org/presta/sitemap-bundle/v/stable.png)](https://packagist.org/packages/presta/sitemap-bundle)
[![Total Downloads](https://poser.pugx.org/presta/sitemap-bundle/downloads.png)](https://packagist.org/packages/presta/sitemap-bundle)

[![PrestaSitemapBundle on Knpbundles](http://knpbundles.com/prestaconcept/PrestaSitemapBundle/badge)](http://knpbundles.com/prestaconcept/PrestaSitemapBundle)


PrestaSitemapBundle is a Symfony2 xml sitemap generator.


:speech_balloon: If you want to have some informations about the projet progression you can register to our [google group][10]


## Overview

For a ready to use demonstration of PrestaSitemap you should check the [prestacms-sandox available on github][11].

Sandbox is also deployed for a live demonstration :

-   [Sitemap index][12]
-   [Sitemap section][13]

## Requirements

* See also the `require` section of [composer.json](composer.json)

## Features ##

 * Sitemapindex
 * Google images, video, mobile and multilang urls
 * Respect constraints (50k items / 10mB per files)
 * No database required
 * Optionnal caching (using DoctrineCacheBundle, disabled by default)

## TL;DR

1. Installation

    ```js
        //composer.json
        "require": {
            //...
            "presta/sitemap-bundle": "dev-master"
        }
    ```

    ```php
        //app/AppKernel.php
        public function registerBundles()
        {
            $bundles = array(
                //...
                new Presta\SitemapBundle\PrestaSitemapBundle(),
            );
        }
    ```

    ```yaml
    #app/config/routing.yml
    PrestaSitemapBundle:
        resource: "@PrestaSitemapBundle/Resources/config/routing.yml"
        prefix:   /
    ```

2. Usage

    For static url there's annotation support in your routes :

    ```php
    /**
     * @Route("/", name="homepage", options={"sitemap" = true})
     */
    ```

    For complexe routes, create a [Closure][3] or a [Service][5] dedicated to your sitemap then add your urls :

    ```php
        function(SitemapPopulateEvent $event) use ($router){
            //get absolute homepage url
            $url = $router->generate('homepage', array(), true);

            //add homepage url to the urlset named default
            $event->getGenerator()->addUrl(
                new UrlConcrete(
                    $url,
                    new \DateTime(),
                    UrlConcrete::CHANGEFREQ_HOURLY,
                    1
                ),
                'default'
            );
        }
    ```

3. Decorated url (images, videos, etc.)

    The [doc][6] is already really short ;)

## Documentation ##

You will find the detailed documentation in the following links :

* [1-Installation.md][1]
* [2-Configuration.md][2]
* [3-Usage-Quick_and_dirty.md][3]
* [4-Usage-Annotation.md][4]
* [5-Usage-Event_Listener.md][5]
* [6-Url_Decorator.md][6]
* [7-Dumper_command.md][7]
* [CHANGELOG.md][8]
* [CONTRIBUTORS.md][9]

## Ask for help ##

:speech_balloon: If you need help about this project you can [post a message on our google group][10]

## Contributing

Pull requests are welcome.

Thanks to
[everyone who has contributed](https://github.com/prestaconcept/PrestaSitemapBundle/graphs/contributors) already.

---

*This project is supported by [PrestaConcept](http://www.prestaconcept.net)*

**Lead Developer** : [@nicolas-bastien](https://github.com/nicolas-bastien)

Released under the MIT License

[1]: Resources/doc/1-Installation.md
[2]: Resources/doc/2-Configuration.md
[3]: Resources/doc/3-Usage-Quick_and_dirty.md
[4]: Resources/doc/4-Usage-Annotation.md
[5]: Resources/doc/5-Usage-Event_Listener.md
[6]: Resources/doc/6-Url_Decorator.md
[7]: Resources/doc/7-Dumper_command.md
[8]: CHANGELOG.md
[9]: Resources/doc/CONTRIBUTORS.md

[10]: https://groups.google.com/forum/?hl=fr&fromgroups#!forum/prestacms-devs
[11]: https://github.com/prestaconcept/prestacms-sandbox
[12]: http://sandbox.prestacms.fr/sitemap.xml
[13]: http://sandbox.prestacms.fr/sitemap.sandbox.xml


[![Bitdeli Badge](https://d2weczhvl823v0.cloudfront.net/prestaconcept/prestasitemapbundle/trend.png)](https://bitdeli.com/free "Bitdeli Badge")

