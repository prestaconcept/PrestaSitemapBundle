<?php

/**
 * This file is part of the PrestaSitemapBundle package.
 *
 * (c) PrestaConcept <www.prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle\Sitemap\Url;

use DateTime;
use Presta\SitemapBundle\Exception;
use Presta\SitemapBundle\Sitemap\Utils;

/**
 * Help to generate video url
 *
 * @see guidelines at http://support.google.com/webmasters/bin/answer.py?hl=en&answer=80472
 *
 * @author David Epely <depely@prestaconcept.net>
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

    /**
     * @var array
     */
    protected $customNamespaces = ['video' => 'http://www.google.com/schemas/sitemap-video/1.1'];

    /**
     * @var string
     */
    protected $thumbnail_loc;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $description;

    //list of optional parameters

    /**
     * @var string|null
     */
    protected $content_loc;

    /**
     * @var string|null
     */
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

    /**
     * @var int|null
     */
    protected $duration;

    /**
     * @var DateTime|null
     */
    protected $expiration_date;

    /**
     * @var int|null
     */
    protected $rating;

    /**
     * @var int|null
     */
    protected $view_count;

    /**
     * @var DateTime|null
     */
    protected $publication_date;

    /**
     * @var string|null
     */
    protected $family_friendly;

    /**
     * @var string|null
     */
    protected $category;

    /**
     * @var array
     */
    protected $restriction_allow = array();

    /**
     * @var array
     */
    protected $restriction_deny = array();

    /**
     * @var string|null
     */
    protected $gallery_loc;

    /**
     * @var string|null
     */
    protected $gallery_loc_title;

    /**
     * @var string|null
     */
    protected $requires_subscription;

    /**
     * @var string|null
     */
    protected $uploader;

    /**
     * @var string|null
     */
    protected $uploader_info;

    /**
     * @var array
     */
    protected $platforms = array();

    /**
     * @var string|null
     */
    protected $platform_relationship;

    /**
     * @var string|null
     */
    protected $live;

    /**
     * multiple prices can be added, see self::addPrice()
     * @var array
     */
    protected $prices = array();

    /**
     * multiple tags can be added, see self::addTag()
     * @var array
     */
    protected $tags = array();

    /**
     * Decorate url with a video
     *
     * @param Url    $urlDecorated
     * @param string $thumnail_loc
     * @param string $title
     * @param string $description
     * @param array  $parameters - the keys to use are the optional properties of this class, (e.g. 'player_loc' =>
     *                           'http://acme.com/player.swf')
     *
     * @throws Exception\GoogleVideoUrlException
     */
    public function __construct(Url $urlDecorated, $thumnail_loc, $title, $description, array $parameters = array())
    {
        foreach ($parameters as $key => $param) {
            $method = Utils::getSetMethod($this, $key);
            $this->$method($param);
        }

        $this->setThumbnailLoc($thumnail_loc);
        $this->setTitle($title);
        $this->setDescription($description);

        if (!$this->content_loc && !$this->player_loc) {
            throw new Exception\GoogleVideoUrlException('The parameter content_loc or player_loc is required');
        }

        if (count($this->platforms) && !$this->platform_relationship) {
            throw new Exception\GoogleVideoUrlException(
                'The parameter platform_relationship is required when platform is set'
            );
        }

        parent::__construct($urlDecorated);
    }

    /**
     * @param string $thumbnail_loc
     *
     * @return GoogleVideoUrlDecorator
     */
    public function setThumbnailLoc($thumbnail_loc)
    {
        $this->thumbnail_loc = $thumbnail_loc;

        return $this;
    }

    /**
     * @return string
     */
    public function getThumbnailLoc()
    {
        return $this->thumbnail_loc;
    }

    /**
     * @param string $title
     *
     * @return GoogleVideoUrlDecorator
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @param string $description
     *
     * @return GoogleVideoUrlDecorator
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @param string $content_loc
     *
     * @return GoogleVideoUrlDecorator
     */
    public function setContentLoc($content_loc)
    {
        $this->content_loc = $content_loc;

        return $this;
    }

    /**
     * @param string $player_loc
     *
     * @return GoogleVideoUrlDecorator
     */
    public function setPlayerLoc($player_loc)
    {
        $this->player_loc = $player_loc;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPlayerLoc()
    {
        return $this->player_loc;
    }

    /**
     * @param string $player_loc_allow_embed
     *
     * @return GoogleVideoUrlDecorator
     */
    public function setPlayerLocAllowEmbed($player_loc_allow_embed)
    {
        if (!in_array($player_loc_allow_embed, [self::PLAYER_LOC_ALLOW_EMBED_YES, self::PLAYER_LOC_ALLOW_EMBED_NO])) {
            throw new Exception\GoogleVideoUrlException(
                sprintf(
                    'The parameter %s must be a valid player_loc_allow_embed.see http://support.google.com/webmasters/bin/answer.py?hl=en&answer=80472#4',
                    $player_loc_allow_embed
                )
            );
        }
        $this->player_loc_allow_embed = $player_loc_allow_embed;

        return $this;
    }

    /**
     * @return string
     */
    public function getPlayerLocAllowEmbed()
    {
        return $this->player_loc_allow_embed;
    }

    /**
     * @param string $player_loc_autoplay
     *
     * @return GoogleVideoUrlDecorator
     */
    public function setPlayerLocAutoplay($player_loc_autoplay)
    {
        $this->player_loc_autoplay = $player_loc_autoplay;

        return $this;
    }

    public function getPlayerLocAutoplay()
    {
        return $this->player_loc_autoplay;
    }

    /**
     * @param int $duration
     *
     * @return GoogleVideoUrlDecorator
     * @throws Exception\GoogleVideoUrlException
     */
    public function setDuration($duration)
    {
        if ($duration < 0 || $duration > 28800) {
            throw new Exception\GoogleVideoUrlException(
                sprintf(
                    'The parameter %s must be a valid duration.see http://support.google.com/webmasters/bin/answer.py?hl=en&answer=80472#4',
                    $duration
                )
            );
        }

        $this->duration = $duration;

        return $this;
    }

    /**
     * @param DateTime $expiration_date
     *
     * @return GoogleVideoUrlDecorator
     */
    public function setExpirationDate(DateTime $expiration_date)
    {
        $this->expiration_date = $expiration_date;

        return $this;
    }

    /**
     * @param float $rating
     *
     * @return GoogleVideoUrlDecorator
     */
    public function setRating($rating)
    {
        if ($rating < 0 || $rating > 5) {
            throw new Exception\GoogleVideoUrlException(
                sprintf(
                    'The parameter %s must be a valid rating.see http://support.google.com/webmasters/bin/answer.py?hl=en&answer=80472#4',
                    $rating
                )
            );
        }

        $this->rating = $rating;

        return $this;
    }

    /**
     * @param int $view_count
     *
     * @return GoogleVideoUrlDecorator
     */
    public function setViewCount($view_count)
    {
        $this->view_count = $view_count;

        return $this;
    }

    /**
     * @param DateTime $publication_date
     *
     * @return GoogleVideoUrlDecorator
     */
    public function setPublicationDate(DateTime $publication_date)
    {
        $this->publication_date = $publication_date;

        return $this;
    }

    /**
     * @param null|string $family_friendly
     *
     * @return GoogleVideoUrlDecorator
     */
    public function setFamilyFriendly($family_friendly = null)
    {
        if (null == $family_friendly) {
            $family_friendly = self::FAMILY_FRIENDLY_YES;
        }

        if (!in_array($family_friendly, [self::FAMILY_FRIENDLY_YES, self::FAMILY_FRIENDLY_NO])) {
            throw new Exception\GoogleVideoUrlException(
                sprintf(
                    'The parameter %s must be a valid family_friendly. see http://support.google.com/webmasters/bin/answer.py?hl=en&answer=80472#4',
                    $family_friendly
                )
            );
        }

        $this->family_friendly = $family_friendly;

        return $this;
    }

    /**
     * @param string $category
     *
     * @return GoogleVideoUrlDecorator
     */
    public function setCategory($category)
    {
        if (strlen($category) > 256) {
            throw new Exception\GoogleVideoUrlException(
                sprintf(
                    'The parameter %s must be a valid category. see http://support.google.com/webmasters/bin/answer.py?hl=en&answer=80472#4',
                    $category
                )
            );
        }

        $this->category = $category;

        return $this;
    }

    /**
     * @param array $countries
     *
     * @return GoogleVideoUrlDecorator
     */
    public function setRestrictionAllow(array $countries)
    {
        $this->restriction_allow = $countries;

        return $this;
    }

    /**
     * @return array
     */
    public function getRestrictionAllow()
    {
        return $this->restriction_allow;
    }

    /**
     * @param array $countries
     *
     * @return GoogleVideoUrlDecorator
     */
    public function setRestrictionDeny(array $countries)
    {
        $this->restriction_deny = $countries;

        return $this;
    }

    /**
     * @return array
     */
    public function getRestrictionDeny()
    {
        return $this->restriction_deny;
    }

    /**
     * @param string $gallery_loc
     *
     * @return GoogleVideoUrlDecorator
     */
    public function setGalleryLoc($gallery_loc)
    {
        $this->gallery_loc = $gallery_loc;

        return $this;
    }

    /**
     * @param string $gallery_loc_title
     *
     * @return GoogleVideoUrlDecorator
     */
    public function setGalleryLocTitle($gallery_loc_title)
    {
        $this->gallery_loc_title = $gallery_loc_title;

        return $this;
    }

    /**
     * @param string $requires_subscription
     *
     * @return GoogleVideoUrlDecorator
     */
    public function setRequiresSubscription($requires_subscription)
    {
        if (!in_array($requires_subscription, [self::REQUIRES_SUBSCRIPTION_YES, self::REQUIRES_SUBSCRIPTION_NO])) {
            throw new Exception\GoogleVideoUrlException(
                sprintf(
                    'The parameter %s must be a valid requires_subscription.see http://support.google.com/webmasters/bin/answer.py?hl=en&answer=80472#4',
                    $requires_subscription
                )
            );
        }

        $this->requires_subscription = $requires_subscription;

        return $this;
    }

    /**
     * @param string $uploader
     *
     * @return GoogleVideoUrlDecorator
     */
    public function setUploader($uploader)
    {
        $this->uploader = $uploader;

        return $this;
    }

    /**
     * @param string $uploader_info
     *
     * @return GoogleVideoUrlDecorator
     */
    public function setUploaderInfo($uploader_info)
    {
        $this->uploader_info = $uploader_info;

        return $this;
    }

    /**
     * @param array $platforms
     *
     * @return GoogleVideoUrlDecorator
     */
    public function setPlatforms(array $platforms)
    {
        $this->platforms = $platforms;

        return $this;
    }

    /**
     * @return array
     */
    public function getPlatforms()
    {
        return $this->platforms;
    }

    /**
     * @param string $platform_relationship
     *
     * @return GoogleVideoUrlDecorator
     */
    public function setPlatformRelationship($platform_relationship)
    {
        $this->platform_relationship = $platform_relationship;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getPlatformRelationship()
    {
        return $this->platform_relationship;
    }

    /**
     * @param string $live
     *
     * @return GoogleVideoUrlDecorator
     */
    public function setLive($live)
    {
        $this->live = $live;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return null|string
     */
    public function getContentLoc()
    {
        return $this->content_loc;
    }

    /**
     * @return int|null
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * @return DateTime|null
     */
    public function getExpirationDate()
    {
        return $this->expiration_date;
    }

    /**
     * @return int|null
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * @return int|null
     */
    public function getViewCount()
    {
        return $this->view_count;
    }

    /**
     * @return DateTime|null
     */
    public function getPublicationDate()
    {
        return $this->publication_date;
    }

    /**
     * @return null|string
     */
    public function getFamilyFriendly()
    {
        return $this->family_friendly;
    }

    /**
     * @return null|string
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @return null|string
     */
    public function getGalleryLoc()
    {
        return $this->gallery_loc;
    }

    /**
     * @return null|string
     */
    public function getGalleryLocTitle()
    {
        return $this->gallery_loc_title;
    }

    /**
     * @return null|string
     */
    public function getRequiresSubscription()
    {
        return $this->requires_subscription;
    }

    /**
     * @return null|string
     */
    public function getUploader()
    {
        return $this->uploader;
    }

    /**
     * @return null|string
     */
    public function getUploaderInfo()
    {
        return $this->uploader_info;
    }

    /**
     * @return string|null
     */
    public function getLive()
    {
        return $this->live;
    }

    /**
     * add price element
     *
     * @param float       $amount
     * @param string      $currency   - ISO 4217 format.
     * @param string|null $type       - rent or own
     * @param string|null $resolution - hd or sd
     *
     * @return GoogleVideoUrlDecorator
     */
    public function addPrice($amount, $currency, $type = null, $resolution = null)
    {
        $this->prices[] = array(
            'amount' => $amount,
            'currency' => $currency,
            'type' => $type,
            'resolution' => $resolution,
        );

        return $this;
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
     *
     * @return GoogleVideoUrlDecorator
     * @throws Exception\GoogleVideoUrlTagException
     */
    public function addTag($tag)
    {
        if (count($this->tags) >= self::TAG_ITEMS_LIMIT) {
            throw new Exception\GoogleVideoUrlTagException(
                sprintf('The tags limit of %d items is exceeded.', self::TAG_ITEMS_LIMIT)
            );
        }

        $this->tags[] = $tag;

        return $this;
    }

    /**
     * @return array
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @inheritdoc
     */
    public function toXml()
    {
        $videoXml = '<video:video>';

        //----------------------
        // required fields
        $videoXml .= '<video:thumbnail_loc>' . Utils::encode($this->getThumbnailLoc()) . '</video:thumbnail_loc>';

        foreach (['title', 'description'] as $paramName) {
            $videoXml .= '<video:' . $paramName . '>' . Utils::render(
                    $this->{Utils::getGetMethod($this, $paramName)}()
                ) . '</video:' . $paramName . '>';
        }
        //----------------------
        //----------------------
        // simple optionnal fields
        if ($this->getCategory()) {
            $videoXml .= '<video:category>' . Utils::render($this->getCategory()) . '</video:category>';
        }
        if ($this->getContentLoc()) {
            $videoXml .= '<video:content_loc>' . Utils::encode($this->getContentLoc()) . '</video:content_loc>';
        }
        foreach ([
                     'duration',
                     'rating',
                     'view_count',
                     'family_friendly',
                     'requires_subscription',
                     'live',
                 ] as $paramName) {
            $getMethod = Utils::getGetMethod($this, $paramName);
            if ($this->$getMethod()) {
                $videoXml .= '<video:' . $paramName . '>' . $this->$getMethod() . '</video:' . $paramName . '>';
            }
        }
        //----------------------
        //----------------------
        // date based optionnal fields
        foreach (['expiration_date', 'publication_date'] as $paramName) {
            $getMethod = Utils::getGetMethod($this, $paramName);
            if ($this->$getMethod()) {
                $videoXml .= '<video:' . $paramName . '>' . $this->$getMethod()->format(
                        'c'
                    ) . '</video:' . $paramName . '>';
            }
        }
        //----------------------
        //----------------------
        // moar complexe optionnal fields
        if ($this->getPlayerLoc()) {
            $allow_embed = ($this->getPlayerLocAllowEmbed()) ? ' allow_embed="' . $this->getPlayerLocAllowEmbed(
                ) . '"' : '';
            $autoplay = ($this->getPlayerLocAutoplay()) ? ' autoplay="' . $this->getPlayerLocAutoplay() . '"' : '';
            $videoXml .= '<video:player_loc' . $allow_embed . $autoplay . '>' . Utils::encode(
                    $this->getPlayerLoc()
                ) . '</video:player_loc>';
        }

        if ($this->getRestrictionAllow()) {
            $videoXml .= '<video:restriction relationship="allow">' . implode(
                    ' ',
                    $this->getRestrictionAllow()
                ) . '</video:restriction>';
        }

        if ($this->getRestrictionDeny()) {
            $videoXml .= '<video:restriction relationship="deny">' . implode(
                    ' ',
                    $this->getRestrictionDeny()
                ) . '</video:restriction>';
        }

        if ($this->getGalleryLoc()) {
            $title = ($this->getGalleryLocTitle()) ? ' title="' . Utils::encode($this->getGalleryLocTitle()) . '"' : '';
            $videoXml .= '<video:gallery_loc' . $title . '>' . Utils::encode(
                    $this->getGalleryLoc()
                ) . '</video:gallery_loc>';
        }

        foreach ($this->getTags() as $tag) {
            $videoXml .= '<video:tag>' . Utils::render($tag) . '</video:tag>';
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
            $videoXml .= '<video:platform relationship="' . $relationship . '">' . implode(
                    ' ',
                    $this->getPlatforms()
                ) . '</video:platform>';
        }
        //----------------------

        $videoXml .= '</video:video>';

        $baseXml = $this->urlDecorated->toXml();

        return str_replace('</url>', $videoXml . '</url>', $baseXml);
    }
}
