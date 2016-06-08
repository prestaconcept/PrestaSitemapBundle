# CHANGELOG

* v1.5.0
  * #114 Updated travis config 33 seconds ago
  * #113 Disable specifying the host and let Symfony's router doing the job 14 minutes ago
  * #112 Enhances performances of deleteExistingSitemaps 39 minutes ago
  * #111 Fixed wrong service parameters and service references in YAML an hour ago
  * #107 Allowing 'sitemap' option to be either true or false or array a day ago
  * #110 Added ability to define default options at bundle configuration level a day ago
  * #109 Allow specifying the section within the route configuration a day ago
  * #108 Allow a custom generator service a day ago
  * #102 Fix tag limit exceeded exception message 2 days ago
  * #106 Fix PHPCS 2 days ago
  * #105 Fixed deprecated usage of LiipDoctrineCacheBundle 2 days ago
  * #104 Fixed exception not chained in RouteAnnotationEventListener 2 days ago
  * #98 Allow UrlConcrete Generation to be overriden 2 days ago
  * #96 Update branch-alias 2 days ago
  * #88 fix Dumper::dump() phpdoc 2 days ago
  * #78 Resolves issue #77 2 days ago
  * #69 Update README.md 2 days ago
  * #67 Make RouteAnnotationEventListener more extensible 2 days ago
  * #54 fix url 2 days ago

* v1.4.0
  * #76 : Fix PSR - travis build success
  * #71 : Add the ability to configure the number of items by sitemap
  * #58 : Remove temporary directory if no urlset was created

* v1.3.0
  * #42 : Remove serialize. The cache provider decides if serialization is the correct way to store the data.
  * #43 : Made Filename for dumped sitemaps configurable
  * 7476b29 : [dumper] add host option
  * dfc52e7 : Clean temporary files
  * 918b49e : [dumper] fake request and use request scope
  * 1553cdc : [dumper] gzip support

* v1.2.0 [tag](https://github.com/prestaconcept/PrestaSitemapBundle/commits/v1.2.0)
  * 09af5c0 : add annotation support for simple routes

* v1.1.0 [tag](https://github.com/prestaconcept/PrestaSitemapBundle/commits/v1.1.0)
  * a865420 : encode url & enclose user defined data in cdata section
  * b672175 : fix parameters in service definition

* v1.0.0 [tag](https://github.com/prestaconcept/PrestaSitemapBundle/commits/v1.0.0)
  * 7ad6eba : Dumper command 
  * Refactor [sf1 plugin]([http://www.symfony-project.org/plugins/prestaSitemapPlugin)
