# PrestaSitemapBundle

## Introduction

What PrestaSitemapBundle can do for you. 
The main goal is generate easily your sitemap.xml with several features you may need:

 * sitemapindex
 * google images, video, mobile and multilang urls
 * respect constraints (50k items / 10mB per files)
 * no database required 
 * optionnal caching (using LiipDoctrineCacheBundle, disabled by default) 

## Installation

1. Add to your composer.json

    //TODO

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

## Usage

The only thing required is : register url for each available pages.
You need to add one or more listeners in your application that provides your urls to 
PrestaSitemapBundle when called. 

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
                                UrlConcrete::CHANGE_FREQUENCY_HOURLY, 
                                1), 'default');
                    });
        }
    }

Then the sitemap can be generated and optionnaly set in cache; 
the sitemapindex will be : http://acme.com/sitemap.xml
So the default section will be available at http://acme.com/sitemap.default.xml . 
Note that if one limit is exceeded a new section will be added (eg. http://acme.com/sitemap.default_1.xml)

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
You need to install LiipDoctrineCacheBundle and specify what kind of cache system to with PrestaSitemap.

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
But it is not allowed to add more than 1000 images for one url (see related documentation)[http://support.google.com/webmasters/bin/answer.py?hl=en&answer=178636&topic=20986&ctx=topic]. 
In this case the generator will throw Exceptions.

So you yo have to set the limit yourself or safely try to add elements to your sitemap :

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
