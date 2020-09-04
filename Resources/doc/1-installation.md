# Bundle installation

Require the bundle as a dependency.

```bash
$ composer require presta/sitemap-bundle
```

Enable it in your application Kernel.

```php
<?php
// config/bundles.php
return [
    //...
    Presta\SitemapBundle\PrestaSitemapBundle::class => ['all' => true],
];
```

Or in your legacy application.

```php
<?php
// app/AppKernel.php
class AppKernel
{
    public function registerBundles()
    {
        $bundles = [
            //...
            new Presta\SitemapBundle\PrestaSitemapBundle(),
        ];

        //...

        return $bundles;
    }
}
```

Import routing.

```yaml
# config/routes/presta_sitemap.yaml
presta_sitemap:
    resource: "@PrestaSitemapBundle/Resources/config/routing.yml"
```

> **Note** you may not be required to import routing if you would only rely on dumped sitemaps.
> Jump to [dedicated documentation](6-dumping-sitemap.md) for more information.


---

« [README](../../README.md) • [Configuration](2-configuration.md) »
