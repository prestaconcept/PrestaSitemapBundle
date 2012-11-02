# PrestaSitemapBundle

[![Build Status](https://secure.travis-ci.org/prestaconcept/PrestaSitemapBundle.png)](http://travis-ci.org/prestaconcept/PrestaSitemapBundle)

## Introduction

What PrestaSitemapBundle can do for you. 
The main goal is generate easily your sitemap.xml with several features you may 
need:

 * sitemapindex
 * google images, video, mobile and multilang urls
 * respect constraints (50k items / 10mB per files)
 * no database required 
 * optionnal caching (using LiipDoctrineCacheBundle, disabled by default) 

## Installation

1. Add to your composer.json

        "require": { 
            //...
            "presta/sitemap-bundle": "dev-master"
        }

2. Enable the bundle

        <?php
        // app/AppKernel.php

        public function registerBundles()
        {
            $bundles = array(
                //...
                new Presta\SitemapBundle\PrestaSitemapBundle(),
            );
        }

3. Add the routes

        #app/config/routing.yml
        PrestaSitemapBundle:
            resource: "@PrestaSitemapBundle/Resources/config/routing.yml"
            prefix:   /

4. [optional] Configure the time to live

    You may want to change the default 3600 seconds max-age set when rendering the
    sitemap. Edit the following configuration in your application.

        #app/config/config.yml
        presta_sitemap:
            timetolive: 3600

    Also this value is used by the cache if you have installed and configured
    liip_doctrine_cache.

5. [optional] Configure base URL for dumper

    If you are going to use sitemap Dumper to create sitemap files by using CLI command
    you have to set the base URL of where you sitemap files will be accessible. The hostname
    of the URL will also be used to make Router generate URLs with hostname.

        #app/config/config.yml
        presta_sitemap:
            dumper_base_url: http://www.example.com/

## Usage

The only thing required is : register url for each available pages.
You need to add one or more listeners in your application that provides your 
urls to PrestaSitemapBundle when called. 

For example in your AcmeDemoBundle :

    <?php

    namespace Acme\DemoBundle;

    use Presta\SitemapBundle\Event\SitemapPopulateEvent;
    use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;
    use Symfony\Component\HttpKernel\Bundle\Bundle;

    class AcmeDemoBundle extends Bundle
    {

        public function boot()
        {
            $router = $this->container->get('router');
            $event  = $this->container->get('event_dispatcher');

            //listen presta_sitemap.populate event
            $event->addListener(
                    SitemapPopulateEvent::onSitemapPopulate, 
                    function(SitemapPopulateEvent $event) use ($router){
                        //get absolute homepage url
                        $url = $router->generate('homepage', array(), true);
                        //add homepage url to the urlset named default
                        $event->getGenerator()->addUrl(new UrlConcrete(
                                $url, 
                                new \DateTime(), 
                                UrlConcrete::CHANGEFREQ_HOURLY, 
                                1), 'default');
                    });
        }
    }

Then the sitemap can be generated and optionnaly set in cache; 
the sitemapindex will be : http://acme.com/sitemap.xml
So the default section will be available at http://acme.com/sitemap.default.xml . 
Note that if one limit is exceeded a new section will be added 
(eg. http://acme.com/sitemap.default_1.xml)

### Sitemap Event Listeners

You can also register your sitemap event listeners by creating service classes implementing
`Presta\SitemapBundle\Service\SitemapListenerInterface` and tagging these services with `presta.sitemap.listener`
tag. This way the services will be lazy-loaded by Symfony's event dispatcher, only when the event is dispatched:

    // services.xml
    <service id="my.sitemap.listener" class="Acme\DemoBundle\EventListener\SitemapListener">
        <tag name="presta.sitemap.listener" />
        <argument type="service" id="router"/>
    </service>

    // Acme/DemoBundle/EventListener/SitemapListener.php
    class SitemapListener implements SitemapListenerInterface
    {

        private $router;

        public function __construct(RouterInterface $router)
        {
            $this->router = $router;
        }

        public function populateSitemap(SitemapPopulateEvent $event)
        {
            $section = $event->getSection();
            if (is_null($section) || $section == 'default') {
                //get absolute homepage url
                $url = $router->generate('homepage', array(), true);
                //add homepage url to the urlset named default
                $event->getGenerator()->addUrl(new UrlConcrete(
                        $url,
                        new \DateTime(),
                        UrlConcrete::CHANGEFREQ_HOURLY,
                        1), 'default');
            }
        }
    }

### Url Decorator

UrlConcrete is the most basic url, but you may want to add images to your url. 
You just need to decorate with GoogleImageUrlDecorator :

    use Presta\SitemapBundle\Sitemap\Url;
    
    //a basic url that provide a xml element following protocol
    $urlBase    = new Url\UrlConcrete('http://acme.com/');
    
    //decorate the url with images for google crawler
    //this also indicates to urlset to use the "image" namespace
    $urlImage   = new Url\GoogleImageUrlDecorator($urlBase);
    
    //add one or more images to the url
    $urlImage->addImage(new Url\GoogleImage('http://acme.com/the-big-picture.jpg'));
    
    //you can add other decorators to the url
    $urlLang    = new Url\GoogleMultilangUrlDecorator($urlImage);

    //... don't forget to add the url to a section
    $event->getGenerator()->addUrl($urlLang);

PrestaSitemapBundle provides those decorators (but you can use your own) : 

 * GoogleImageUrlDecorator
 * GoogleMobileUrlDecorator
 * GoogleMultilangUrlDecorator
 * GoogleVideoUrlDecorator

## Configuration

### Cache [optional] 

Each sitemaps can be stored in your cache system :

PrestaSitemapBundle uses LiipDoctrineCacheBundle to store Cache. 
This bundle provides an abstract access to any Doctrine Common Cache classes.
You need to install LiipDoctrineCacheBundle and specify what kind of cache 
system to use with PrestaSitemap.

 * Follow the instruction to install [LiipDoctrineCacheBundle](http://packagist.org/packages/liip/doctrine-cache-bundle).
 * Configure a service for PrestaSitemap, this is an exemple with php-apc :

        #config.yml
        liip_doctrine_cache:
            namespaces:
                presta_sitemap:
                    type: apc


## Deeper informations

As you can see the bundle takes care about limit constraints and automatically 
divide sections for example because this is allowed.
But it is not allowed to add more than 1000 images for one url 
[see related documentation](http://support.google.com/webmasters/bin/answer.py?hl=en&answer=178636&topic=20986&ctx=topic). 
In this case the generator will throw Exceptions.

So you yo have to set the limit yourself or safely try to add elements to your 
sitemap :

    //...
    $url = new Url\GoogleImageUrlDecorator(new Url\UrlConcrete('http://acme.com/'));
    
    try {
        foreach($bigCollectionNotSafe as $loc) {
            $url->addImage(new Url\GoogleImage($loc));
        }
    } catch (Presta\SitemapBundle\Exception $e) {
        //Sir, the area is safe, Sir!
    }
    
    $event->getGenerator()->addUrl($url, 'default');
    //...

This case is similar for tags in GoogleVideoUrlDecorator.

## Dumper command

If you want to dump your sitemaps to files and serve them statically (like assets are served)
you can use `presta:sitemap:dump` console command. This can also be useful if you have really large sitemaps.
The command dumps them into files w/o consuming much memory.

To use it you have to set `dumper_base_url` in your config.yml (see above).
The command accepts single argument which is the folder where to dump sitemaps to, it defaults to `web`, since
most of the people keep the sitemaps in the root of their sites.
The command always creates `sitemap.xml` file as sitemaps index. The other files are named according to section names
you provide, when adding URLs in your `SitemapPopulateEvent` event listeners.

    > app/console presta:sitemap:dump
    Dumping all sections of sitemaps into web directory
    Created the following sitemap files
        main.xml
        main_0.xml
        sitemap.xml

The command first creates all sitemap files in a temporary location. Once all of the files are created
it deletes matching (by section names) files from your target directory and copies newly prepared files in place.
This happens in almost atomic way. In case anything went wrong during sitemap generation your existing sitemap files
will be untouched.

Dumper command can also be used to regenerate just a part of sitemaps (by section name). In order to do that
you have to supply `--section=name` option to the command. It will regenerate only sections with that name
and update corresponding part of sitemap index file, leaving other sitemap references intact.

To make use of these feature your Event listeners should check `$event->getSection()` in the following way:

    if (is_null($event->getSection()) || $event->getSection() == 'mysection') {
        $event->getGenerator()->addUrl(new UrlConcrete(
                                $url,
                                new \DateTime(),
                                UrlConcrete::CHANGEFREQ_HOURLY,
                                1), 'mysection');
    }


