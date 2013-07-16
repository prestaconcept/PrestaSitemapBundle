# Installation

1. Add to your `composer.json`

    ```js
        "require": { 
            //...
            "presta/sitemap-bundle": "dev-master"
        }
    ```

2. Enable the bundle in your `app/AppKernel.php`

    ```php
        public function registerBundles()
        {
            $bundles = array(
                //...
                new Presta\SitemapBundle\PrestaSitemapBundle(),
            );
        }
    ```

3. [optional] Add the routes to your `app/config/routing.yml`

    ```yaml
    PrestaSitemapBundle:
        resource: "@PrestaSitemapBundle/Resources/config/routing.yml"
        prefix:   /
    ```
