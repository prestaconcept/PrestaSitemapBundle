<?php

/*
 * This file is part of the PrestaSitemapBundle package.
 *
 * (c) PrestaConcept <https://prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle\Tests\Integration\Controller;

use Presta\SitemapBundle\Messenger\DumpSitemapMessage;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

final class MessengerController
{
    /**
     * @Route("/dispatch-message", name="dispatch_message")
     */
    public function dispatch(Request $request, MessageBusInterface $bus): Response
    {
        $bus->dispatch(new DumpSitemapMessage(null, null, null, ['gzip' => $request->query->getBoolean('gzip')]));

        return new Response(__FUNCTION__);
    }
}
