<?php

/*
 * This file is part of the prestaSitemapPlugin package.
 * (c) David Epely <depely@prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle\Sitemap\Url;

use Presta\SitemapBundle\Exception;

/**
 * Help to generate video url 
 * 
 * @see guidelines at http://support.google.com/webmasters/bin/answer.py?hl=en&answer=80472
 * 
 * @author David Epely 
 */
class GoogleVideoUrlDecorator extends UrlDecorator
{
    const PLAYER_LOC_ALLOW_EMBED_YES = 'yes';
    const PLAYER_LOC_ALLOW_EMBED_NO = 'no';
    const FAMILY_FRIENDLY_YES = 'yes';
    const FAMILY_FRIENDLY_NO = 'no';
    const RELATIONSHIP_ALLOW = 'allow';
    const RELATIONSHIP_DENY = 'deny';
    const PRICE_TYPE_RENT = 'rent';
    const PRICE_TYPE_OWN = 'own';
    const PRICE_RESOLUTION_HD = 'HD';
    const PRICE_RESOLUTION_SD = 'SD';
    const REQUIRES_SUBSCRIPTION_YES = 'yes';
    const REQUIRES_SUBSCRIPTION_NO = 'no';
    const PLATFORM_WEB = 'web';
    const PLATFORM_MOBILE = 'mobile';
    const PLATFORM_TV = 'tv';
    const PLATFORM_RELATIONSHIP_ALLOW = 'allow';
    const PLATFORM_RELATIONSHIP_DENY = 'deny';
    const LIVE_YES = 'yes';
    const LIVE_NO = 'no';
    const TAG_ITEMS_LIMIT = 32;

    protected $customNamespaces = array('video' => 'http://www.google.com/schemas/sitemap-video/1.1');
    protected $thumbnail_loc;
    protected $title;
    protected $description;
    //list of optional parameters 
    protected $content_loc;
    protected $player_loc;

    /**
     * allow google to embed video in search results
     * @var string
     */
    protected $player_loc_allow_embed;

    /**
     * user defined string for flashvar parameters in embed tag (e.g. autoplay="ap=1")
     * @var string
     */
    protected $player_loc_autoplay;
    protected $duration;
    protected $expiration_date;
    protected $rating;
    protected $view_count;
    protected $publication_date;
    protected $family_friendly;
    protected $category;
    protected $restriction_allow = array();
    protected $restriction_deny = array();
    protected $gallery_loc;
    protected $gallery_loc_title;
    protected $requires_subscription;
    protected $uploader;
    protected $uploader_info;
    protected $platforms = array();
    protected $platform_relationship;
    protected $live;
    //multiple prices can be added, see self::addPrice()
    protected $prices = array();
    //multiple tags can be added, see self::addTag()
    protected $tags = array();

    /**
     * Decorate url with a video
     * 
     * @param Url $urlDecorated
     * @param type $thumnail_loc
     * @param type $title
     * @param type $description
     * @param array $parameters - the keys to use are the optional properties of this class, (e.g. 'player_loc' => 'http://acme.com/player.swf')
     * @throws Exception\GoogleVideoUrlException 
     */
    public function __construct(
    Url $urlDecorated, $thumnail_loc, $title, $description, array $parameters = array())
    {
        foreach ($parameters as $key => $param) {
            $method = $this->getSetMethod($key);
            $this->$method($param);
        }

        $this->setThumbnailLoc($thumnail_loc);
        $this->setTitle($title);
        $this->setDescription($description);

        if (!$this->content_loc && !$this->player_loc) {
            throw new Exception\GoogleVideoUrlException('The parameter content_loc or player_loc is required');
        }

        if (count($this->platforms) && !$this->platform_relationship) {
            throw new Exception\GoogleVideoUrlException('The parameter platform_relationship is required when platform is set');
        }

        parent::__construct($urlDecorated);
    }

    public function setThumbnailLoc($thumbnail_loc)
    {
        $this->thumbnail_loc = $thumbnail_loc;
    }

    public function getThumbnailLoc()
    {
        return $this->thumbnail_loc;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function setContentLoc($content_loc)
    {
        $this->content_loc = $content_loc;
    }

    public function setPlayerLoc($player_loc)
    {
        $this->player_loc = $player_loc;
    }

    public function getPlayerLoc()
    {
        return $this->player_loc;
    }

    public function setPlayerLocAllowEmbed($player_loc_allow_embed)
    {
        if (!in_array($player_loc_allow_embed, array(self::PLAYER_LOC_ALLOW_EMBED_YES, self::PLAYER_LOC_ALLOW_EMBED_NO))) {
            throw new Exception\GoogleVideoUrlException(sprintf('The parameter %s must be a valid player_loc_allow_embed.see http://support.google.com/webmasters/bin/answer.py?hl=en&answer=80472#4', $player_loc_allow_embed));
        }
        $this->player_loc_allow_embed = $player_loc_allow_embed;
    }

    public function getPlayerLocAllowEmbed()
    {
        return $this->player_loc_allow_embed;
    }

    /**
     * @param string $player_loc_autoplay 
     */
    public function setPlayerLocAutoplay($player_loc_autoplay)
    {
        $this->player_loc_autoplay = $player_loc_autoplay;
    }

    public function getPlayerLocAutoplay()
    {
        return $this->player_loc_autoplay;
    }

    /**
     * @param int $duration
     * @return void
     * @throws Exception\GoogleVideoUrlException 
     */
    public function setDuration($duration)
    {
        if (!is_numeric($duration) || $duration < 0 || $duration > 28800) {
            throw new Exception\GoogleVideoUrlException(sprintf('The parameter %s must be a valid duration.see http://support.google.com/webmasters/bin/answer.py?hl=en&answer=80472#4', $duration));
        }

        $this->duration = $duration;
    }

    public function setExpirationDate(\DateTime $expiration_date)
    {
        $this->expiration_date = $expiration_date;
    }

    /**
     * @param float $rating 
     */
    public function setRating($rating)
    {
        if (!is_numeric($rating) || $rating < 0 || $rating > 5) {
            throw new Exception\GoogleVideoUrlException(sprintf('The parameter %s must be a valid rating.see http://support.google.com/webmasters/bin/answer.py?hl=en&answer=80472#4', $rating));
        }

        $this->rating = $rating;
    }

    public function setViewCount($view_count)
    {
        if (!is_int($view_count)) {
            throw new Exception\GoogleVideoUrlException(sprintf('The parameter %s must be a valid view count.see http://support.google.com/webmasters/bin/answer.py?hl=en&answer=80472#4', $view_count));
        }

        $this->view_count = $view_count;
    }

    public function setPublicationDate(\DateTime $publication_date)
    {
        $this->publication_date = $publication_date;
    }

    /**
     * @param string $family_friendly 
     */
    public function setFamilyFriendly($family_friendly = null)
    {
        if (null == $family_friendly) {
            $family_friendly = self::FAMILY_FRIENDLY_YES;
        }

        if (!in_array($family_friendly, array(self::FAMILY_FRIENDLY_YES, self::FAMILY_FRIENDLY_NO))) {
            throw new Exception\GoogleVideoUrlException(sprintf('The parameter %s must be a valid family_friendly. see http://support.google.com/webmasters/bin/answer.py?hl=en&answer=80472#4', $family_friendly));
        }

        $this->family_friendly = $family_friendly;
    }

    public function setCategory($category)
    {
        if (strlen($category) > 256) {
            throw new Exception\GoogleVideoUrlException(sprintf('The parameter %s must be a valid category. see http://support.google.com/webmasters/bin/answer.py?hl=en&answer=80472#4', $category));
        }

        $this->category = $category;
    }

    public function setRestrictionAllow(array $countries = array())
    {
        $this->restriction_allow = $countries;
    }

    public function getRestrictionAllow()
    {
        return $this->restriction_allow;
    }

    public function setRestrictionDeny(array $countries = array())
    {
        $this->restriction_deny = $countries;
    }

    public function getRestrictionDeny()
    {
        return $this->restriction_deny;
    }

    public function setGalleryLoc($gallery_loc)
    {
        $this->gallery_loc = $gallery_loc;
    }

    public function setGalleryLocTitle($gallery_loc_title)
    {
        $this->gallery_loc_title = $gallery_loc_title;
    }

    public function setRequiresSubscription($requires_subscription)
    {
        $this->requires_subscription = $requires_subscription;
    }

    public function setUploader($uploader)
    {
        $this->uploader = $uploader;
    }

    public function setUploaderInfo($uploader_info)
    {
        $this->uploader_info = $uploader_info;
    }

    public function setPlatforms(array $platforms)
    {
        $this->platforms = $platforms;
    }

    public function getPlatforms()
    {
        return $this->platforms;
    }

    public function setPlatformRelationship($platform_relationship)
    {
        $this->platform_relationship = $platform_relationship;
    }

    public function getPlatformRelationship()
    {
        return $this->platform_relationship;
    }

    public function setLive($live)
    {
        $this->live = $live;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getContentLoc()
    {
        return $this->content_loc;
    }

    public function getDuration()
    {
        return $this->duration;
    }

    public function getExpirationDate()
    {
        return $this->expiration_date;
    }

    public function getRating()
    {
        return $this->rating;
    }

    public function getViewCount()
    {
        return $this->view_count;
    }

    public function getPublicationDate()
    {
        return $this->publication_date;
    }

    public function getFamilyFriendly()
    {
        return $this->family_friendly;
    }

    public function getCategory()
    {
        return $this->category;
    }

    public function getGalleryLoc()
    {
        return $this->gallery_loc;
    }

    public function getGalleryLocTitle()
    {
        return $this->gallery_loc_title;
    }

    public function getRequiresSubscription()
    {
        return $this->requires_subscription;
    }

    public function getUploader()
    {
        return $this->uploader;
    }

    public function getUploaderInfo()
    {
        return $this->uploader_info;
    }

    public function getLive()
    {
        return $this->live;
    }

    /**
     * add price element 
     * 
     * @param float $price
     * @param string $currency - ISO 4217 format. 
     * @param string $type - rent or own
     * @param string $resolution  - hd or sd
     */
    public function addPrice($amount, $currency, $type = null, $resolution = null)
    {
        $this->prices[] = array(
            'amount' => $amount,
            'currency' => $currency,
            'type' => $type,
            'resolution' => $resolution
        );
    }

    /**
     * list of defined prices with price, currency, type and resolution
     * 
     * @return array
     */
    public function getPrices()
    {
        return $this->prices;
    }

    /**
     * @param string $tag
     * @throws Exception\GoogleVideoUrlTagException 
     */
    public function addTag($tag)
    {
        if (count($this->tags) >= self::TAG_ITEMS_LIMIT) {
            throw new Exception\GoogleVideoUrlTagException(sprintf('The parameter %s must be a valid family_friendly. see http://support.google.com/webmasters/bin/answer.py?hl=en&answer=80472#4'));
        }

        $this->tags[] = $tag;
    }

    public function getTags()
    {
        return $this->tags;
    }

    /**
     * decorate w/ the video element before the closing tag
     * 
     * @return string 
     */
    public function toXml()
    {
        $videoXml = '<video:video>';

        //----------------------
        // required fields
        foreach (array('thumbnail_loc', 'title', 'description') as $paramName) {
            $getMethod = $this->getGetMethod($paramName);
            $videoXml .= '<video:' . $paramName . '>' . $this->$getMethod() . '</video:' . $paramName . '>';
        }
        //----------------------
        //----------------------
        // simple optionnal fields
        foreach (array('category', 'content_loc', 'duration', 'rating', 'view_count', 'family_friendly', 'requires_subscription', 'live') as $paramName) {
            $getMethod = $this->getGetMethod($paramName);
            if ($this->$getMethod()) {
                $videoXml .= '<video:' . $paramName . '>' . $this->$getMethod() . '</video:' . $paramName . '>';
            }
        }
        //----------------------
        //----------------------
        // date based optionnal fields
        foreach (array('expiration_date', 'publication_date') as $paramName) {
            $getMethod = $this->getGetMethod($paramName);
            if ($this->$getMethod()) {
                $videoXml .= '<video:' . $paramName . '>' . $this->$getMethod()->format('c') . '</video:' . $paramName . '>';
            }
        }
        //----------------------
        //----------------------
        // moar complexe optionnal fields
        if ($this->getPlayerLoc()) {
            $allow_embed = ($this->getPlayerLocAllowEmbed()) ? ' allow_embed="' . $this->getPlayerLocAllowEmbed() . '"' : '';
            $autoplay = ($this->getPlayerLocAutoplay()) ? ' autoplay="' . $this->getPlayerLocAutoplay() . '"' : '';
            $videoXml .= '<video:player_loc' . $allow_embed . $autoplay . '>' . $this->getPlayerLoc() . '</video:player_loc>';
        }

        if ($this->getRestrictionAllow()) {
            $videoXml .= '<video:restriction relationship="allow">' . implode(' ', $this->getRestrictionAllow()) . '</video:restriction>';
        }

        if ($this->getRestrictionDeny()) {
            $videoXml .= '<video:restriction relationship="deny">' . implode(' ', $this->getRestrictionDeny()) . '</video:restriction>';
        }

        if ($this->getGalleryLoc()) {
            $title = ($this->getGalleryLocTitle()) ? ' title="' . $this->getGalleryLocTitle() . '"' : '';
            $videoXml .= '<video:gallery_loc' . $title . '>' . $this->getGalleryLoc() . '</video:gallery_loc>';
        }

        foreach ($this->getTags() as $tag) {
            $videoXml .= '<video:tag>' . $tag . '</video:tag>';
        }

        foreach ($this->getPrices() as $price) {
            $type = ($price['type']) ? ' type="' . $price['type'] . '"' : '';
            $resolution = ($price['resolution']) ? ' resolution="' . $price['resolution'] . '"' : '';
            $videoXml .= '<video:price currency="' . $price['currency'] . '"' . $type . $resolution . '>' . $price['amount'] . '</video:price>';
        }

        if ($this->getUploader()) {
            $info = ($this->getUploaderInfo()) ? ' info="' . $this->getUploaderInfo() . '"' : '';
            $videoXml .= '<video:uploader' . $info . '>' . $this->getUploader() . '</video:uploader>';
        }

        if (count($this->getPlatforms())) {
            $relationship = $this->getPlatformRelationship();
            $videoXml .= '<video:platform relationship="' . $relationship . '">' . implode(' ', $this->getPlatforms()) . '</video:platform>';
        }
        //----------------------

        $videoXml .= '</video:video>';

        $baseXml = $this->urlDecorated->toXml();
        return str_replace('</url>', $videoXml . '</url>', $baseXml);
    }

    /**
     * verify method affiliated to given param
     * 
     * @param string $name
     * @return string
     * @throws Exception\GoogleVideoUrlException 
     */
    protected function getSetMethod($name)
    {
        $methodName = 'set' . str_replace(' ', '', ucwords(str_replace('_', ' ', $name)));

        if (!method_exists($this, $methodName)) {
            throw new Exception\GoogleVideoUrlException(sprintf('The parameter %s is unknown', $name));
        }

        return $methodName;
    }

    /**
     * verify method affiliated to given param
     * 
     * @param string $name
     * @return string
     * @throws Exception\GoogleVideoUrlException 
     */
    protected function getGetMethod($name)
    {
        $methodName = 'get' . str_replace(' ', '', ucwords(str_replace('_', ' ', $name)));

        if (!method_exists($this, $methodName)) {
            throw new Exception\GoogleVideoUrlException(sprintf('The parameter %s is unknown', $name));
        }

        return $methodName;
    }
}
