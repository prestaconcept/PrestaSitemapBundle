<?php 

namespace Presta\SitemapBundle\Sitemap;

use Presta\SitemapBundle\Sitemap\Section;
use Presta\SitemapBundle\Sitemap\Url;
use Presta\SitemapBundle\Sitemap\Url\Image;

/**
 * Manage generation of groups of urls
 * 
 * @author  Christophe Dolivet
 * @version 1.0 - 4 août 2009 - Christophe Dolivet
 */
class Builder
{
	protected $section;
	
	protected $maxFileSize;
	
	protected $maxUrlPerFile;
	
	protected $maxImagePerUrl;
	
	protected $reservedFileSizeForHeader;
	
	
	protected $rootUrl;
	
	/**
	 * Constructor
	 */
	public function __construct($rootUrl)
	{
		$this->rootUrl  = $rootUrl;
		
		// make thoses parameters optionnal
		$this->setMaxUrlPerFile(49999)
			->setMaxFileSize(10485760)
			->setMaxImagePerUrl(9999)
			->setReservedFileSizeForHeader(5000);
	}
	
	public function setSection($section)
	{
		$section  = $section;
	}
	
	public function setMaxUrlPerFile($maxUrlPerFile)
	{
		$this->maxUrlPerFile  = max(1, $maxUrlPerFile);
		return $this;
	}
	
	public function setMaxFileSize($maxFileSize)
	{
		$this->maxFileSize 	  = $maxFileSize;
		return $this;
	}
	
	public function setMaxImagePerUrl($maxImagePerUrl)
	{
		$this->maxImagePerUrl = $maxImagePerUrl;
		return $this;
	}
	
	public function setReservedFileSizeForHeader($reservedFileSizeForHeader)
	{
		$this->reservedFileSizeForHeader = $reservedFileSizeForHeader;
		return $this;
	}
	
	/**
	 * Add a sitemap url to the whole results
	 * 
	 * @author  Christophe Dolivet
	 * @since   1.0 - 15 juil. 2009 - Christophe Dolivet
	 * @version 1.0 - 15 juil. 2009 - Christophe Dolivet
	 * @param Url $url - The object used for defining the sitemap entry
	 */
	public function buildSectionFiles(Section $section)
	{
		$this->validate();
		$section->validate();
		
		$xmlFiles = $this->buildInnerFileContents($section);
        
		foreach($xmlFiles as &$xmlFileContent)
		{
			$xmlFileContent = $this->decorateXmlFile($xmlFileContent);
		}
		
		return $xmlFiles;
	}	
	
	protected function validate()
	{
		if(!preg_match('@://.*/$@', $this->rootUrl))
		{
			throw new \RuntimeException(sprintf('Root url must end with a "/" ("%s" given)', $this->rootUrl));
		}
	}

	
	
	protected function decorateXmlFile($xmlFileContent)
	{
		return sprintf('<?xml version="1.0" encoding="UTF-8" ?>
			<urlset
				xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
				xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd"
				xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
				xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
				%s
			</urlset>',
			$xmlFileContent);
	}
	
	protected function buildInnerFileContents($section)
	{
            $section->setGenerationDate(new \DateTime());
		$xmlFiles = array();
		$urlCounter = 0;
		$fileCounter = 0;
		foreach($section->getUrls() as $url)
		{
			$urlXml = $this->urlToXml($url);
			// move to next file if maxUrl or maxSize reached for current file
			$maxUrlPerFileReached = $urlCounter >= $this->maxUrlPerFile;
			
            $maxFileSizeReached = array_key_exists($fileCounter, $xmlFiles) && strlen($xmlFiles[$fileCounter]) + strlen($urlXml) > $this->maxFileSize - $this->reservedFileSizeForHeader;
			
            if(!array_key_exists($fileCounter, $xmlFiles))
            {
                $xmlFiles[$fileCounter] = '';
            }
            
			// we need to use a new output file
			if($maxUrlPerFileReached || $maxFileSizeReached)
			{
				$fileCounter++;
				$urlCounter = 0;
			}
		
			// add url to current xml file content
			$xmlFiles[$fileCounter] .= $urlXml;
			$urlCounter++;
		}
        
        return $xmlFiles;
	}
	
	
	protected function urlToXml(Url $url)
	{
		ob_start();
		?>
		<url>
			<loc><?php echo $this->stringToXmlValue($url->getLocation()) ?></loc>
			<?php if( !is_null( $url->getLastModificationDate() ) ): ?>
				<lastmod><?php echo $url->getLastModificationDate()->format('c') ?></lastmod>
			<?php endif; ?>
			<?php if( !is_null( $url->getChangeFrequency() ) ): ?>
				<changefreq><?php echo $url->getChangeFrequency() ?></changefreq>
			<?php endif; ?>
			<?php if( !is_null( $url->getPriority() ) ): ?>
				<priority><?php echo $url->getPriority() ?></priority>
			<?php endif; ?>
			<?php // add images tags ?>
			<?php foreach(array_slice($url->getImages(), 0, $this->maxImagePerUrl) as $urlImage ): ?>
				<?php echo $this->urlImageToXml($urlImage) ?>
			<?php endforeach; ?>
			<?php // add mobile tag extension ?>
			<?php if( $url->getMobile() ):?>
				<mobile:mobile/>
			<?php endif;?>
		</url>
		<?php
		return ob_get_clean();
	}
	
	protected function urlImageToXml(Url $url)
	{
		ob_start();
		?>
		<image:image>
			<image:loc><?php echo $this->stringToXmlValue($this->getLocation()) ?></image:loc>
			<?php if( !is_null( $this->getCaption() ) ): ?>
				<image:caption><?php echo $this->stringToXmlValue($this->getCaption()) ?></image:caption>
			<?php endif; ?>
			<?php if( !is_null( $this->getGeoLocation() ) ): ?>
				<image:geo_location><?php echo $this->stringToXmlValue($this->getGeoLocation()) ?></image:geo_location>
			<?php endif; ?>
			<?php if( !is_null( $this->getTitle() ) ): ?>
				<image:title><?php echo $this->stringToXmlValue($this->getTitle()) ?></image:title>
			<?php endif; ?>
			<?php if( !is_null( $this->getLicense() ) ): ?>
				<image:license><?php echo $this->stringToXmlValue($this->getLicense()) ?></image:license>
			<?php endif; ?>
		</image:image>
		<?php
		return ob_get_clean();
	}
	
	
	protected function absolutizeUrl($string)
	{
		if(strpos($string, '://') === false)
		{
			$string = sprintf('%s%s', $this->rootUrl, preg_replace('@^(/?)@', $string));
		}
		return $string;
	}
	
	protected function stringToXmlValue($string)
	{
		// try to convert to UTF-8 if 'mb_convert_encoding' is available
		if(function_exists( 'mb_convert_encoding' ))
		{
			$string	= mb_convert_encoding( $string, 'UTF-8', 'auto' );
		}
		
		// convert string and encode specials htmlcharacters (doesn't encode already encoded characters)
		$string	= htmlspecialchars( $string, ENT_QUOTES, 'UTF-8', false );
		
		return $string;
	}
}