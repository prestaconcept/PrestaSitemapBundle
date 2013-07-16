# Usage Annotations

You can use annotations to configure any route which does not use parameters (e.g. your static pages such as '/about',
'/faq').

The supported sitemap parameters are:

 * lastmod: a text string that can be parsed by \DateTime (default: 'now')
 * changefreq: a text string that matches a constant defined in UrlConcrete (default: 'daily')
 * priority: a number between 0 and 1 (default: 1)

```php
<?php

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage", options={"sitemap" = true})
     *                                      ^ include in the sitemap with default parameters
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }

    /**
     * @Route("/faq", name="faq", options={"sitemap" = {"priority" = 0.7 }})
     *                                      ^ override the priority parameter
     * @Template()
     */
    public function faqAction()
    {
        return array();
    }

    /**
     * @Route("/about", name="about", options={"sitemap" = {"priority" = 0.7, "changefreq" = "weekly" }})
     *                                      ^ override the priority and changefreq parameters
     * @Template()
     */
    public function aboutAction()
    {
        return array();
    }


}
```