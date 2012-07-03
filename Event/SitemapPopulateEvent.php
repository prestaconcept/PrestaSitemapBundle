<?php

/*
 * This file is part of the prestaSitemapPlugin package.
 * (c) David Epely <depely@prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Presta\SitemapBundle\Service\Generator;

/**
 * Manage populate event 
 * 
 * @author depely
 */
class SitemapPopulateEvent extends Event
{
    const onSitemapPopulate = 'presta_sitemap.populate';

    protected $generator;

    public function __construct(Generator $generator)
    {
        $this->generator = $generator;
    }

    /**
     * @return Generator
     */
    public function getGenerator()
    {
        return $this->generator;
    }
}