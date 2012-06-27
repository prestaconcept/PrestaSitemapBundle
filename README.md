# PrestaSitemapBundle

## Introduction

Generate your sitemap.xml :

 * sitemapindex
 * google images, video, mobile and multilang urls
 * limit constraints (50k items / 10mB per files)
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
You just need to decorate with GoogleImageUrlDecorator

//TODO

Hmmm may be you also need to say this url is for mobile; please decorate with
GoogleMobileUrlDecorator

//TODO



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
