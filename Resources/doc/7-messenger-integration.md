# Messenger integration

If you have installed [Symfony Messenger](https://symfony.com/doc/current/messenger.html#installation), then you can 
dispatch `Presta\SitemapBundle\Messenger\DumpSitemapMessage` message to your transport to handle it asynchronously or
synchronously.

## [Routing the message to your transport](https://symfony.com/doc/current/messenger.html#routing-messages-to-a-transport)

```yaml
# config/packages/messenger.yaml
framework:
    messenger:
        transports:
            async: "%env(MESSENGER_TRANSPORT_DSN)%"

        routing:
            # async is whatever name you gave your transport above
            'Presta\SitemapBundle\Messenger\DumpSitemapMessage':  async
```

After configuring the message routing dispatch the message like this:

```php
// src/Controller/DefaultController.php
namespace App\Controller;

use Presta\SitemapBundle\Messenger\DumpSitemapMessage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Messenger\MessageBusInterface;

class DefaultController extends AbstractController
{
    public function index(MessageBusInterface $bus)
    {
        // this will dispatch to dump all sitemap sections
        $bus->dispatch(new DumpSitemapMessage());

        // If you wish to dump a single section, change the base url, target dir
        // and gzip option you can provide these through the message constructor
        $bus->dispatch(new DumpSitemapMessage('custom_section', 'https://sitemap.acme.org', '/path/to/sitemap', ['gzip' => true]));
    }
}
```

---

« [Dumping sitemap](6-dumping-sitemap.md) • [README](../../README.md) »
