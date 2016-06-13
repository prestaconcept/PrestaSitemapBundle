# Url Decorator

`UrlConcrete` is the most basic url, but you may want to add images to your url. 
You just need to decorate with `GoogleImageUrlDecorator`:

```php
use Presta\SitemapBundle\Sitemap\Url;
    
// a basic url that provide a xml element following protocol
$urlBase    = new Url\UrlConcrete('http://acme.com/');
    
// decorate the url with images for google crawler
// this also indicates to urlset to use the "image" namespace
$urlImage   = new Url\GoogleImageUrlDecorator($urlBase);
    
// add one or more images to the url
$urlImage->addImage(new Url\GoogleImage('http://acme.com/the-big-picture.jpg'));
    
// you can add other decorators to the url
$urlLang    = new Url\GoogleMultilangUrlDecorator($urlImage);

// ... don't forget to add the url to a section
$event->getUrlContainer()->addUrl($urlLang);
```

PrestaSitemapBundle provides those decorators (but you can use your own) : 

 * GoogleImageUrlDecorator
 * GoogleMobileUrlDecorator
 * GoogleMultilangUrlDecorator
 * GoogleNewsUrlDecorator
 * GoogleVideoUrlDecorator

## Deeper informations

As you can see the bundle takes care about limit constraints and automatically 
divide sections for example because this is allowed.
But it is not allowed to add more than 1000 images for one url 
[see related documentation](http://support.google.com/webmasters/bin/answer.py?hl=en&answer=178636&topic=20986&ctx=topic). 
In this case the generator will throw Exceptions.

So you yo have to set the limit yourself or safely try to add elements to your 
sitemap :

```php
use Presta\SitemapBundle\Sitemap\Url;

$url = new Url\GoogleImageUrlDecorator(new Url\UrlConcrete('http://acme.com/'));
    
try {
    foreach($bigCollectionNotSafe as $loc) {
        $url->addImage(new Url\GoogleImage($loc));
    }
} catch (Presta\SitemapBundle\Exception $e) {
    // Sir, the area is safe, Sir!
}
    
$event->getUrlContainer()->addUrl($url, 'default');
```

This case is similar for tags in GoogleVideoUrlDecorator.

The GoogleNewsUrlDecorator helps to generate google news sitemap elements.
A news URL has some tag limitations which are checked by the decorator.
For example are the google finance stock_tickers related to a news limited to 5.
The decorator will throw an exception if a limit is passed:

```php
use Presta\SitemapBundle\Sitemap\Url;
use Presta\SitemapBundle\Exception;

$url = new Url\GoogleNewsUrlDecorator(new Url\UrlConcrete('http://acme.com/'),
    'The Example Times', 'en', new \DateTime(),
    'An example news article'
);

try {
    $url->setStockTickers(array(
        'NYSE:OWW',
        'NASDAQ:GTAT',
        'NYSE:AOL',
        'NASDAQ:ENDP',
        'CVE:GTA',
        'NASDAQ:IMGN'
    ));
} catch (Exception\GoogleNewsUrlException $e) {
    // limit of 5 tickers passed
}
```

For more information on the news URL limitations [see the related documentation](https://support.google.com/webmasters/answer/74288?hl=en).
