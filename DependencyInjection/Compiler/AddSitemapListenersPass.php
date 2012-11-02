<?php

/*
 * This file is part of the prestaSitemapPlugin package.
 * (c) David Epely <depely@prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Presta\SitemapBundle\Event\SitemapPopulateEvent;

/**
 * Registering services tagged with presta.sitemap.listener as actual event listeners
 *
 * @author Konstantin Tjuterev <kostik.lv@gmail.com>
 */
class AddSitemapListenersPass implements CompilerPassInterface
{
    /**
     * Adds services tagges as presta.sitemap.listener as event listeners for
     * corresponding sitemap event
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     *
     * @throws \InvalidArgumentException
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('event_dispatcher')) {
            return;
        }

        $definition = $container->getDefinition('event_dispatcher');

        foreach ($container->findTaggedServiceIds('presta.sitemap.listener') as $id => $tags) {
            $class = $container->getDefinition($id)->getClass();

            $refClass = new \ReflectionClass($class);
            $interface = 'Presta\SitemapBundle\Service\SitemapListenerInterface';
            if (!$refClass->implementsInterface($interface)) {
                throw new \InvalidArgumentException(sprintf('Service "%s" must implement interface "%s".', $id, $interface));
            }
            $definition->addMethodCall(
                'addListenerService',
                array(SitemapPopulateEvent::onSitemapPopulate, array($id, 'populateSitemap'))
            );
        }
    }
}
