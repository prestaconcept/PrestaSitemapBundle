<?php 

namespace Presta\SitemapBundle\Sitemap;


/**
 * Manage generation of groups of urls
 * 
 * @author  Christophe Dolivet
 * @version 1.0 - 4 août 2009 - Christophe Dolivet
 */
class Section
{
	const VALID_SECTION_NAME_PATTERN	= '@^[0-9a-zA-Z]+$@';
	
	protected $name;							// section name
	protected $lifetime;
	protected $urls;			// Array of associated Url objects 
        protected $generation_date;
	
	/**
	 * Constructor for the sitemapSection
	 */
	public function __construct($name, $lifetime)
	{
		// check sectionName validity
		if( !preg_match( self::VALID_SECTION_NAME_PATTERN, $name ) )
		{
			throw new Exception(sprintf('The section name must match the following pattern "%s"', self::VALID_SECTION_NAME_PATTERN));
		}
		
		$this->name		= $name;
		$this->lifetime	= $lifetime;
		$this->urls 	= array();
        $this->generation_date = new \DateTime();
	}
	
	
	/**
	 * Add a sitemap url to the whole results
	 * 
	 * @author  Christophe Dolivet
	 * @since   1.0 - 15 juil. 2009 - Christophe Dolivet
	 * @version 1.0 - 15 juil. 2009 - Christophe Dolivet
	 * @param Url $url - The object used for defining the sitemap entry
	 */
	public function addUrl(Url $url )
	{
		$this->urls[] = $url;
	}	
	
	
	/**
	 * Return the prestaSitemapUrl object associated to this section object 
	 * 
	 * @author  Christophe Dolivet
	 * @since   1.0 - 15 juil. 2009 - Christophe Dolivet
	 * @version 1.0 - 15 juil. 2009 - Christophe Dolivet
	 * @return Array of prestaSitemapUrl objects
	 */
	public function getUrls()
	{
		return $this->urls;
	}
	
	
	/**
	 * Return the section name
	 * 
	 * @author  Christophe Dolivet
	 * @since   1.0 - 15 juil. 2009 - Christophe Dolivet
	 * @version 1.0 - 15 juil. 2009 - Christophe Dolivet
	 * @return String
	 */
	public function getName()
	{
		return $this->name;
	}
	
	
	/**
	 * Delete empty urls from the urls associated to this section
	 * 
	 * @author  Christophe Dolivet
	 * @since   1.0 - 22 juil. 2009 - Christophe Dolivet
	 * @version 1.1 - 5 oct. 2010 - Alain Flaus <aflaus@prestaconcept.net>
	 */
	public function validate()
	{
		foreach( $this->urls as $key => $url )
		{
			if($url->validate() === false) 
			{
				unset( $this->urls[ $url ] );
				unset( $url );
			}
		}
	}
        
        public function getGenerationDate()
        {
            return $this->generation_date;
        }
        
        public function setGenerationDate(\DateTime $date)
        {
            $this->generation_date = $date;
        }
                
}