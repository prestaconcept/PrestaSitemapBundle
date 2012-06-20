<?php

namespace Presta\SitemapBundle\Sitemap;

/**
 * Class used for managing url entites
 * 
 * @author  Christophe Dolivet
 * @version 1.0 - 4 août 2009 - Christophe Dolivet
 */
class Url
{
    const CHANGE_FREQUENCY_ALWAYS   = 'always';
    const CHANGE_FREQUENCY_HOURLY   = 'hourly';
    const CHANGE_FREQUENCY_DAILY    = 'daily';
    const CHANGE_FREQUENCY_WEEKLY   = 'weekly';
    const CHANGE_FREQUENCY_MONTHLY  = 'monthly';
    const CHANGE_FREQUENCY_YEARLY   = 'yearly';
    const CHANGE_FREQUENCY_NEVER    = 'never';

    protected
            $location, // absolute url
            $lastModificationDate, // last modifcaiotn date
            $changeFrequency, // change frequency
            $priority, // priority
            $images = array(), // array of UrlImage
            $isMobile = false;      // is mobile or not (default : not)

    /**
     * Construct a new Url mainly identified by it's url
     * 
     * @author  Christophe Dolivet
     * @since   1.0 - 15 juil. 2009 - Christophe Dolivet
     * @version 1.0 - 15 juil. 2009 - Christophe Dolivet
     * @param Mixed $location[optional] - Valid parameters for a call to url_for()
     * @param DateTime $lastModificationDate[optional]
     * @param String $changeFrequency[optional]
     * @param Float $priority[optional]
     */
    public function __construct($location, \DateTime $lastModificationDate = null, $changeFrequency = null, $priority = null)
    {
        // use a callback for the location as $location can be an array of parameters
        $this->setLocation($location);
        $this->setLastModificationDate($lastModificationDate);
        $this->setChangeFrequency($changeFrequency);
        $this->setPriority($priority);
    }

    /**
     * Define the location base on url_for method
     * 
     * @author  Christophe Dolivet
     * @since   1.0 - 15 juil. 2009 - Christophe Dolivet
     * @version 1.0 - 15 juil. 2009 - Christophe Dolivet
     * @param String $internal_uri
     * @return Url
     */
    public function setLocation($location)
    {
        $this->location = $location;
        return $this;
    }

    /**
     * return the location
     * 
     * @author  Christophe Dolivet
     * @since   1.0 - 15 juil. 2009 - Christophe Dolivet
     * @version 1.0 - 15 juil. 2009 - Christophe Dolivet
     * @return String
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Define the last modificaiton date of this entry
     * 
     * Produce ISO 8601 date string (valid W3C Datetime format)
     * 
     * @author  Christophe Dolivet
     * @since   1.0 - 15 juil. 2009 - Christophe Dolivet
     * @version 1.0 - 15 juil. 2009 - Christophe Dolivet
     * @param DateTime $lastModificationDate - DateTime object or null used for defining the last modification date of this entry
     * @return Url
     */
    public function setLastModificationDate(\DateTime $lastModificationDate = null)
    {
        $this->lastModificationDate = $lastModificationDate;
        return $this;
    }

    /**
     * return the last modification date
     * 
     * @author  Christophe Dolivet
     * @since   1.0 - 15 juil. 2009 - Christophe Dolivet
     * @version 1.0 - 15 juil. 2009 - Christophe Dolivet
     * @return String
     */
    public function getLastModificationDate()
    {
        return $this->lastModificationDate;
    }

    /**
     * Define the change frequency of this entry
     * 
     * @author  Christophe Dolivet
     * @since   1.0 - 15 juil. 2009 - Christophe Dolivet
     * @version 1.0 - 15 juil. 2009 - Christophe Dolivet
     * @param String $changeFrequency - String or null value used for defining the change frequency
     * @return Url
     */
    public function setChangeFrequency($changeFrequency)
    {
        // move this in the "builder"
        switch ($changeFrequency) {
            case self::CHANGE_FREQUENCY_ALWAYS:
            case self::CHANGE_FREQUENCY_HOURLY:
            case self::CHANGE_FREQUENCY_DAILY:
            case self::CHANGE_FREQUENCY_WEEKLY:
            case self::CHANGE_FREQUENCY_MONTHLY:
            case self::CHANGE_FREQUENCY_YEARLY:
            case self::CHANGE_FREQUENCY_NEVER:
                $this->changeFrequency = $changeFrequency;
                break;
            default:
                null;
        }

        return $this;
    }

    /**
     * return the change frequency
     * 
     * @author  Christophe Dolivet
     * @since   1.0 - 15 juil. 2009 - Christophe Dolivet
     * @version 1.0 - 15 juil. 2009 - Christophe Dolivet
     * @return String
     */
    public function getChangeFrequency()
    {
        return $this->changeFrequency;
    }

    /**
     * Define the priority of this entry
     * 
     * @author  Christophe Dolivet
     * @since   1.0 - 15 juil. 2009 - Christophe Dolivet
     * @version 1.0 - 15 juil. 2009 - Christophe Dolivet
     * @param Float $priority - Float or null value used for defining the priority
     * @return Url
     */
    public function setPriority($priority)
    {
        // TODO: move this in the "builder"
        if (!is_null($priority) && is_numeric($priority) && $priority >= 0 && $priority <= 1) {
            $this->priority = sprintf('%01.1f', $priority);
        } else {
            // TODO: loguer qu'il y a eu une erreur?
            $this->priority = null;
        }
        return $this;
    }

    /**
     * return the priority
     * 
     * @author  Christophe Dolivet
     * @since   1.0 - 15 juil. 2009 - Christophe Dolivet
     * @version 1.0 - 15 juil. 2009 - Christophe Dolivet
     * @return String
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * add a UrlImage to the current Url
     * 
     * @author	Alain Flaus <aflaus@prestaconcept.net>
     * @version	1.0 - 5 oct. 2010 - Alain Flaus <aflaus@prestaconcept.net>
     * @since	5 oct. 2010 - Alain Flaus <aflaus@prestaconcept.net>
     * @param 	UrlImage $UrlImage
     * @return 	Url
     */
    public function addImage(Url\Image $image)
    {
        $this->images[] = $image;
        return $this;
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
        // remove invalid images
        foreach ($this->images as $key => $image) {
            if ($image->validate() === false) {
                unset($this->images[$key]);
                unset($image);
            }
        }

        $location = $this->getLocation();
        $isValid = !empty($location);
        return $isValid;
    }

    /**
     * return the sitemapUrlImages associated to the current sitemapUrl
     * 
     * @author	Alain Flaus <aflaus@prestaconcept.net>
     * @version	1.0 - 5 oct. 2010 - Alain Flaus <aflaus@prestaconcept.net>
     * @since	5 oct. 2010 - Alain Flaus <aflaus@prestaconcept.net>
     * @return 	array of UrlImages
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * set if it's an url for mobile access
     * 
     * @author	Matthieu Crinquand <mcrinquand@prestaconcept.net>
     * @version	1.0 - 12 juil. 2011 - Matthieu Crinquand <mcrinquand@prestaconcept.net>
     * @since	12 juil. 2011 - Matthieu Crinquand <mcrinquand@prestaconcept.net>
     * @param 	boolean $mobile
     * @return  Url
     */
    public function setMobile($mobile)
    {
        $this->isMobile = $mobile;
        return $this;
    }

    /**
     * get if it's an url for mobile access
     * 
     * @author	Matthieu Crinquand <mcrinquand@prestaconcept.net>
     * @version	1.0 - 12 juil. 2011 - Matthieu Crinquand <mcrinquand@prestaconcept.net>
     * @since	12 juil. 2011 - Matthieu Crinquand <mcrinquand@prestaconcept.net>
     * @param 	boolean 
     */
    public function isMobile()
    {
        return $this->isMobile;
    }
}
