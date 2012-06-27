<?php
namespace Presta\SitemapBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Presta\SitemapBundle\Service\Generator;


class SitemapPopulateEvent extends Event
{
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