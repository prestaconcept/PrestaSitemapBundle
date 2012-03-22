<?php 

namespace Presta\SitemapBundle\Cache;

use Presta\SitemapBundle\Sitemap\Url;
use Presta\SitemapBundle\Sitemap\Url\Image;

/**
 * Manage generation of groups of urls
 * 
 * @author  Christophe Dolivet
 * @version 1.0 - 4 août 2009 - Christophe Dolivet
 */
class FileCache
{
	protected $cacheDir;
	
	public function __construct($cacheDir)
	{
		$this->cacheDir = $cacheDir;
	}
	
	public function get($key)
	{
		
	}
	
	public function set($key, $content)
	{
		
	}
}