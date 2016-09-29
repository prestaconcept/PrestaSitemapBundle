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
use Presta\SitemapBundle\Event\SitemapRouteEvent;

/**
 * Registering services tagged with presta.route.listener as actual event listeners
 *
 * @author Mathieu Lemoine <mlemoine@mlemoine.name>
 */
class AddSitemapRouteListenersPass implements CompilerPassInterface
{
    /**
     * @inheritdoc
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('event_dispatcher') && !$container->hasAlias('event_dispatcher')) {
            return;
        }

        $definition = $container->findDefinition('event_dispatcher');

        foreach ($container->findTaggedServiceIds('presta.route.listener') as $id => $tags) {
            $class = $container->getDefinition($id)->getClass();

            $refClass = new \ReflectionClass($class);
            $interface = 'Presta\SitemapBundle\Service\SitemapRouteListenerInterface';
            if (!$refClass->implementsInterface($interface)) {
                throw new \InvalidArgumentException(sprintf('Service "%s" must implement interface "%s".', $id, $interface));
            }
            $definition->addMethodCall(
                'addListenerService',
                array(SitemapRouteEvent::ON_SITEMAP_ROUTE, array($id, 'decorateUrl'))
            );
        }
    }
}
