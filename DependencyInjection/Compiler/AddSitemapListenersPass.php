<?php

/**
 * This file is part of the PrestaSitemapBundle package.
 *
 * (c) PrestaConcept <www.prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle\DependencyInjection\Compiler;

use Presta\SitemapBundle\Event\SitemapPopulateEvent;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Registering services tagged with presta.sitemap.listener as actual event listeners
 *
 * @author Konstantin Tjuterev <kostik.lv@gmail.com>
 */
class AddSitemapListenersPass implements CompilerPassInterface
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

        foreach ($container->findTaggedServiceIds('presta.sitemap.listener') as $id => $tags) {
            $class = $container->getDefinition($id)->getClass();

            @trigger_error('The service "'.$id.'" was tagged with "presta.sitemap.listener", which is deprecated. Use Symfony event listeners/subscribers instead.', E_USER_DEPRECATED);

            $refClass = new \ReflectionClass($class);
            $interface = 'Presta\SitemapBundle\Service\SitemapListenerInterface';
            if (!$refClass->implementsInterface($interface)) {
                throw new \InvalidArgumentException(sprintf('Service "%s" must implement interface "%s".', $id, $interface));
            }
            $definition->addMethodCall(
                'addListenerService',
                array(SitemapPopulateEvent::ON_SITEMAP_POPULATE, array($id, 'populateSitemap'))
            );
        }
    }
}
