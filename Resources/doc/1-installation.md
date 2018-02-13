# Bundle installation

Require the bundle as a dependency.

```bash
composer require presta/sitemap-bundle
```

Enable it in your application Kernel.

```php
    //app/AppKernel.php
    public function registerBundles()
    {
        $bundles = [
            //...
            new Presta\SitemapBundle\PrestaSitemapBundle(),
        ];
    }
```

Import routing.

```yaml
#config/routes/presta_sitemap.yml
presta_sitemap:
    resource: "@PrestaSitemapBundle/Resources/config/routing.yml"
```

> **Note** you may not be required to import routing if you would only rely on dumped sitemaps.
> Jump to [dedicated documentation](7-dump-sitemap.md) for more information.


---

« [README](../../README.md) • [Configuration](2-configuration.md) »
