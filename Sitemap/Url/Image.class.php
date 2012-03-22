<?php

namespace Presta\SitemapBundle\Sitemap\Url;


/**
 * Class used for managing image's url entites
 * 
 * @author	Alain Flaus <aflaus@prestaconcept.net>
 * @version	SVN: $Id: prestaSitemapUrlImage.class.php 220 2010-10-07 08:51:23Z cdolivet $ 1.0 - 5 oct. 2010 - Alain Flaus <aflaus@prestaconcept.net>
 * @since	5 oct. 2010 - Alain Flaus <aflaus@prestaconcept.net>
 */
class Image
{	
	protected
		$location,				// absolute url
		$caption,				// alt
		$geo_location,
		$title,
		$license;

		
	/**
	 * Construct a new prestaSitemapUrlImage mainly identified by it's url
	 * 
	 * @author	Alain Flaus <aflaus@prestaconcept.net>
	 * @version	1.0 - 5 oct. 2010 - Alain Flaus <aflaus@prestaconcept.net>
	 * @since	5 oct. 2010 - Alain Flaus <aflaus@prestaconcept.net>
	 * @param 	Mixed $location[optional] - Valid parameters for a call to image_path()
	 * @param 	String $caption[optional]
	 * @param 	String $geo_location[optional]
	 * @param 	String $title[optional]
	 * @param 	String $license[optional]
	 */
	public function __construct($location, $caption = null, $geo_location = null, $title = null, $license = null )
	{
		// use a callback for the location as $location can be an array of parameters
		$this->setLocation( $location )
			->setCaption( $caption )
			->setGeoLocation( $geo_location )
			->setTitle( $title )
			->setLicense( $license );
	}
	
	
	
	
	/**
	 * Define the location base on image_path method
	 * 
	 * @author  Christophe Dolivet
	 * @since   1.0 - 15 juil. 2009 - Christophe Dolivet
	 * @version 1.0 - 15 juil. 2009 - Christophe Dolivet
	 * @param 	String $internal_uri
	 * @return 	Image
	 */
	public function setLocation( $location )
	{
		$this->location	= $location;
		return $this;
	}
	
	/**
	 * return the location
	 * 
	 * @author  Christophe Dolivet
	 * @since   1.0 - 15 juil. 2009 - Christophe Dolivet
	 * @version 1.0 - 15 juil. 2009 - Christophe Dolivet
	 * @return 	String
	 */
	public function getLocation()
	{
		return $this->location;
	}
	
	
	/**
	 * Define the caption
	 * 
	 * @author	Alain Flaus <aflaus@prestaconcept.net>
	 * @version	1.0 - 5 oct. 2010 - Alain Flaus <aflaus@prestaconcept.net>
	 * @since	5 oct. 2010 - Alain Flaus <aflaus@prestaconcept.net>
	 * @param 	String $caption
	 * @return 	prestaSitemapUrlImage
	 */
	public function setCaption( $caption )
	{
		$this->caption = $caption;
		return $this;
	}
	
	
	/**
	 * Return the caption
	 * 
	 * @author	Alain Flaus <aflaus@prestaconcept.net>
	 * @version	1.0 - 5 oct. 2010 - Alain Flaus <aflaus@prestaconcept.net>
	 * @since	5 oct. 2010 - Alain Flaus <aflaus@prestaconcept.net>
	 * @return 	String
	 */
	public function getCaption()
	{
		return $this->caption;
	}
	
	
	/**
	 * Define the geo_location
	 * 
	 * @author	Alain Flaus <aflaus@prestaconcept.net>
	 * @version	1.0 - 5 oct. 2010 - Alain Flaus <aflaus@prestaconcept.net>
	 * @since	5 oct. 2010 - Alain Flaus <aflaus@prestaconcept.net>
	 * @param 	String $caption
	 * @return 	prestaSitemapUrlImage
	 */
	public function setGeoLocation( $geo_location )
	{
		$this->geo_location = $geo_location;
		return $this;
	}
	
	
	/**
	 * Return the geo_location
	 * 
	 * @author	Alain Flaus <aflaus@prestaconcept.net>
	 * @version	1.0 - 5 oct. 2010 - Alain Flaus <aflaus@prestaconcept.net>
	 * @since	5 oct. 2010 - Alain Flaus <aflaus@prestaconcept.net>
	 * @return 	String
	 */
	public function getGeoLocation()
	{
		return $this->geo_location;
	}
	
	
	/**
	 * Define the title
	 * 
	 * @author	Alain Flaus <aflaus@prestaconcept.net>
	 * @version	1.0 - 5 oct. 2010 - Alain Flaus <aflaus@prestaconcept.net>
	 * @since	5 oct. 2010 - Alain Flaus <aflaus@prestaconcept.net>
	 * @param 	String $title
	 * @return 	prestaSitemapUrlImage
	 */
	public function setTitle( $title )
	{
		$this->title = $title;
		return $this;
	}
	
	
	/**
	 * Return the title
	 * 
	 * @author	Alain Flaus <aflaus@prestaconcept.net>
	 * @version	1.0 - 5 oct. 2010 - Alain Flaus <aflaus@prestaconcept.net>
	 * @since	5 oct. 2010 - Alain Flaus <aflaus@prestaconcept.net>
	 * @return 	String
	 */
	public function getTitle()
	{
		return $this->title;
	}
	
	
	/**
	 * Define the license
	 * 
	 * @author	Alain Flaus <aflaus@prestaconcept.net>
	 * @version	1.0 - 5 oct. 2010 - Alain Flaus <aflaus@prestaconcept.net>
	 * @since	5 oct. 2010 - Alain Flaus <aflaus@prestaconcept.net>
	 * @param 	String $license
	 * @return 	prestaSitemapUrlImage
	 */
	public function setLicense( $license )
	{
		$this->license = $license;
		return $this;
	}
	
	
	/**
	 * Return the license
	 * 
	 * @author	Alain Flaus <aflaus@prestaconcept.net>
	 * @version	1.0 - 5 oct. 2010 - Alain Flaus <aflaus@prestaconcept.net>
	 * @since	5 oct. 2010 - Alain Flaus <aflaus@prestaconcept.net>
	 * @return 	String
	 */
	public function getLicense()
	{
		return $this->license;
	}
	
	
	/**
	 * Return the xml content of this object
	 * 
	 * @author	Alain Flaus <aflaus@prestaconcept.net>
	 * @version	1.0 - 5 oct. 2010 - Alain Flaus <aflaus@prestaconcept.net>
	 * @since	5 oct. 2010 - Alain Flaus <aflaus@prestaconcept.net>
	 * @return 	String
	 */
	public function toXML()
	{
		ob_start();
?>
	<image:image>
		<image:loc><?php echo $this->getLocation() ?></image:loc>
	<?php if( !is_null( $this->getCaption() ) ): ?>
		<image:caption><?php echo $this->getCaption() ?></image:caption>
	<?php endif; ?>
	<?php if( !is_null( $this->getGeoLocation() ) ): ?>
		<image:geo_location><?php echo $this->getGeoLocation() ?></image:geo_location>
	<?php endif; ?>
	<?php if( !is_null( $this->getTitle() ) ): ?>
		<image:title><?php echo $this->getTitle() ?></image:title>
	<?php endif; ?>
	<?php if( !is_null( $this->getLicense() ) ): ?>
		<image:license><?php echo $this->getLicense() ?></image:license>
	<?php endif; ?>
	</image:image>
<?php
		return ob_get_clean();
	}
	
	/**
	 * Delete images with an empty location
	 * 
	 * @author	Alain Flaus <aflaus@prestaconcept.net>
	 * @version	1.0 - 5 oct. 2010 - Alain Flaus <aflaus@prestaconcept.net>
	 * @since	5 oct. 2010 - Alain Flaus <aflaus@prestaconcept.net>
	 * @param 	$index
	 * @return 	Url
	 */
	public function validate()
	{
		$location = $this->getLocation();
		$isValid = !empty($location); 
		return $isValid;
	}
		
	
	/**
	 * Convert datas to utf-8, encode special xml characters, and refuse string length >= 2048
	 * 
	 * @author  Christophe Dolivet
	 * @since   1.0 - 31 juil. 2009 - Christophe Dolivet
	 * @version 1.0 - 31 juil. 2009 - Christophe Dolivet
	 * @param 	$string
	 * @return 	String
	 */
	protected static function toValidUtf8LocationContent( $string )
	{
		if( !is_null( $string ) )
		{
			// try to convert to UTF-8 if 'mb_convert_encoding' is available
			$string	= function_exists( 'mb_convert_encoding' ) ? mb_convert_encoding( $string, 'UTF-8', 'auto' ) : $string;
			
			// convert string and encode specials htmlcharacters (doesn't encode already encoded characters)
			$string	= htmlspecialchars( $string, ENT_QUOTES, 'UTF-8', false );
			
			$length	= function_exists( 'mb_strlen' ) ? mb_strlen( $string, 'UTF-8' ) : strlen( $string );
			if( $length >= 2048 )
			{
				$string	= null;
			}
		}
		return $string;
	}
}