# Usage Annotations

You can use annotations to configure any route which does not use parameters (e.g. your static pages such as '/about',
'/faq').

The supported sitemap parameters are:

 * section: a text string that represent the section in which to store the URL
 * lastmod: a text string that can be parsed by \DateTime (default: 'now')
 * changefreq: a text string that matches a constant defined in UrlConcrete (default: 'daily')
 * priority: a number between 0 and 1 (default: 1)

## Annotation

```php
<?php

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage",
     *      options={"sitemap" = true}
     * )
     */
    public function indexAction()
    {
        //...
    }

    /**
     * @Route("/faq", name="faq",
     *      options={"sitemap" = {"priority" = 0.7 }}
     * )
     */
    public function faqAction()
    {
        //...
    }

    /**
     * @Route("/about", name="about",
     *      options={"sitemap" = {"priority" = 0.7, "changefreq" = "weekly" }}
     * )
     */
    public function aboutAction()
    {
        //...
    }

    /**
     * @Route("/contact", name="contact",
     *      options={"sitemap" = {"priority" = 0.7, "changefreq" = "weekly", "section" = "misc" }}
     * )
     */
    public function contactAction()
    {
        //...
    }
}
```

## YAML

```yml
homepage:
    path:     /
    defaults: { _controller: AppBundle:Default:index }
    options:
        sitemap: true

faq:
    path:     /faq
    defaults: { _controller: AppBundle:Default:faq }
    options:
        sitemap:
            priority: 0.7

about:
    path:     /about
    defaults: { _controller: AppBundle:Default:about }
    options:
        sitemap:
            priority: 0.7
            changefreq: weekly

contact:
    path:     /contact
    defaults: { _controller: AppBundle:Default:contact }
    options:
        sitemap:
            priority: 0.7
            changefreq: weekly
            section: misc
```

## XML

```xml
<?xml version="1.0" encoding="UTF-8" ?>

<routes xmlns="http://symfony.com/schema/routing"
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:schemaLocation="http://symfony.com/schema/routing http://symfony.com/schema/routing/routing-1.0.xsd">

    <route id="homepage" path="/">
        <default key="_controller">AppBundle:Default:index</default>
        <option key="sitemap">true</option>
    </route>

    <route id="faq" path="/faq">
        <default key="_controller">AppBundle:Default:faq</default>
        <option key="sitemap">
            {"priority":"0.7"}
        </option>
    </route>

    <route id="about" path="/about">
        <default key="_controller">AppBundle:Default:about</default>
        <option key="sitemap">
            {"priority":"0.7", "changefreq":"weekly"}
        </option>
    </route>

    <route id="contact" path="/contact">
        <default key="_controller">AppBundle:Default:contact</default>
        <option key="sitemap">
            {"priority":"0.7", "changefreq":"weekly", "section":"misc"}
        </option>
    </route>

</routes>
```
