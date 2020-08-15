# Static routes usage


You just need to configure an option to your route, so the bundle knows you want to expose it.

The supported sitemap parameters are:

 * `"section"`: string that represent the section identifier in which to store the URL (default: `"default"`)
 * `"lastmod"`: a valid datetime as string (default: `"now"`)
 * `"changefreq"`: change frequency of your resource, 
 one of `"always"`, `"hourly"`, `"daily"`, `"weekly"`, `"monthly"`, `"yearly"`, `"never"` (default: `"daily"`)
 * `"priority"`: a number between `0` and `1` (default: `1`)

> **Note** you can change defaults in the bundle configuration.
> Jump to [dedicated documentation](2-configuration.md) for more information.


## Annotation

```php
<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Route;

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
    defaults: { _controller: App\Controller\DefaultController::index }
    options:
        sitemap: true

faq:
    path:     /faq
    defaults: { _controller: App\Controller\DefaultController::faq }
    options:
        sitemap:
            priority: 0.7

about:
    path:     /about
    defaults: { _controller: App\Controller\DefaultController::about }
    options:
        sitemap:
            priority: 0.7
            changefreq: weekly

contact:
    path:     /contact
    defaults: { _controller: App\Controller\DefaultController::contact }
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
        <default key="_controller">App\Controller\DefaultController::index</default>
        <option key="sitemap">true</option>
    </route>

    <route id="faq" path="/faq">
        <default key="_controller">App\Controller\DefaultController::faq</default>
        <option key="sitemap">
            {"priority":"0.7"}
        </option>
    </route>

    <route id="about" path="/about">
        <default key="_controller">App\Controller\DefaultController::about</default>
        <option key="sitemap">
            {"priority":"0.7", "changefreq":"weekly"}
        </option>
    </route>

    <route id="contact" path="/contact">
        <default key="_controller">App\Controller\DefaultController::contact</default>
        <option key="sitemap">
            {"priority":"0.7", "changefreq":"weekly", "section":"misc"}
        </option>
    </route>

</routes>
```


---

« [Configuration](2-configuration.md) • [Dynamic routes usage](4-dynamic-routes-usage.md) »
