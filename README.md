# PrestaSitemapBundle

[![Build Status](https://secure.travis-ci.org/prestaconcept/PrestaSitemapBundle.png)](http://travis-ci.org/prestaconcept/PrestaSitemapBundle)

[![PrestaSitemapBundle on Knpbundles](http://knpbundles.com/prestaconcept/PrestaSitemapBundle/badge)](http://knpbundles.com/prestaconcept/PrestaSitemapBundle)


## Introduction

What PrestaSitemapBundle can do for you. 
The main goal is generate easily your sitemap.xml with several features you may 
need:

 * sitemapindex
 * google images, video, mobile and multilang urls
 * respect constraints (50k items / 10mB per files)
 * no database required 
 * optionnal caching (using LiipDoctrineCacheBundle, disabled by default) 

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

## Full Documentation

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

[1]: Resources/doc/1-Installation.md
[2]: Resources/doc/2-Configuration.md
[3]: Resources/doc/3-Usage-Quick_and_dirty.md
[4]: Resources/doc/4-Usage-Annotation.md
[5]: Resources/doc/5-Usage-Event_Listener.md
[6]: Resources/doc/6-Url_Decorator.md
[7]: Resources/doc/7-Dumper_command.md
[8]: CHANGELOG.md
[9]: Resources/doc/CONTRIBUTORS.md

[![Bitdeli Badge](https://d2weczhvl823v0.cloudfront.net/prestaconcept/prestasitemapbundle/trend.png)](https://bitdeli.com/free "Bitdeli Badge")

