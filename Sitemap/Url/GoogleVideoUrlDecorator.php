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

use DateTimeInterface;
use Presta\SitemapBundle\Exception;

/**
 * Help to generate video url
 *
 * @see guidelines at http://support.google.com/webmasters/bin/answer.py?hl=en&answer=80472
 *
 * @author David Epely <depely@prestaconcept.net>
 */
class GoogleVideoUrlDecorator extends UrlDecorator
{
    const LIMIT_ITEMS = 1000;

    /**
     * @var array
     */
    protected $customNamespaces = ['video' => 'http://www.google.com/schemas/sitemap-video/1.1'];

    /**
     * @var string
     */
    protected $videoXml = '';

    /**
     * @var bool
     */
    protected $limitItemsReached = false;

    /**
     * @var int
     */
    protected $countItems = 0;

    /**
     * @param GoogleVideo $video
     *
     * @return GoogleVideoUrlDecorator
     */
    public function addVideo(GoogleVideo $video)
    {
        if ($this->isFull()) {
            throw new Exception\GoogleVideoException('The video limit has been exceeded');
        }

        $this->videoXml .= $video->toXml();

        //---------------------
        //Check limits
        if (++$this->countItems >= self::LIMIT_ITEMS) {
            $this->limitItemsReached = true;
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function toXml()
    {
        $baseXml = $this->urlDecorated->toXml();

        if ($this->video) {
            $this->addVideo($this->video);
        }

        return str_replace('</url>', $this->videoXml . '</url>', $baseXml);
    }

    /**
     * @return bool
     */
    public function isFull()
    {
        return $this->limitItemsReached;
    }

    // BC Compatibility layer

    /**
     * @deprecated Use GoogleVideo::PLAYER_LOC_ALLOW_EMBED_YES instead
     */
    const PLAYER_LOC_ALLOW_EMBED_YES = 'yes';

    /**
     * @deprecated Use GoogleVideo::PLAYER_LOC_ALLOW_EMBED_NO instead
     */
    const PLAYER_LOC_ALLOW_EMBED_NO = 'no';

    /**
     * @deprecated Use GoogleVideo::FAMILY_FRIENDLY_YES instead
     */
    const FAMILY_FRIENDLY_YES = 'yes';

    /**
     * @deprecated Use GoogleVideo::FAMILY_FRIENDLY_NO instead
     */
    const FAMILY_FRIENDLY_NO = 'no';

    /**
     * @deprecated Use GoogleVideo::RELATIONSHIP_ALLOW instead
     */
    const RELATIONSHIP_ALLOW = 'allow';

    /**
     * @deprecated Use GoogleVideo::RELATIONSHIP_DENY instead
     */
    const RELATIONSHIP_DENY = 'deny';

    /**
     * @deprecated Use GoogleVideo::PRICE_TYPE_RENT instead
     */
    const PRICE_TYPE_RENT = 'rent';

    /**
     * @deprecated Use GoogleVideo::PRICE_TYPE_OWN instead
     */
    const PRICE_TYPE_OWN = 'own';

    /**
     * @deprecated Use GoogleVideo::PRICE_RESOLUTION_HD instead
     */
    const PRICE_RESOLUTION_HD = 'HD';

    /**
     * @deprecated Use GoogleVideo::PRICE_RESOLUTION_SD instead
     */
    const PRICE_RESOLUTION_SD = 'SD';

    /**
     * @deprecated Use GoogleVideo::REQUIRES_SUBSCRIPTION_YES instead
     */
    const REQUIRES_SUBSCRIPTION_YES = 'yes';

    /**
     * @deprecated Use GoogleVideo::REQUIRES_SUBSCRIPTION_NO instead
     */
    const REQUIRES_SUBSCRIPTION_NO = 'no';

    /**
     * @deprecated Use GoogleVideo::PLATFORM_WEB instead
     */
    const PLATFORM_WEB = 'web';

    /**
     * @deprecated Use GoogleVideo::PLATFORM_MOBILE instead
     */
    const PLATFORM_MOBILE = 'mobile';

    /**
     * @deprecated Use GoogleVideo::PLATFORM_TV instead
     */
    const PLATFORM_TV = 'tv';

    /**
     * @deprecated Use GoogleVideo::PLATFORM_RELATIONSHIP_ALLOW instead
     */
    const PLATFORM_RELATIONSHIP_ALLOW = 'allow';

    /**
     * @deprecated Use GoogleVideo::PLATFORM_RELATIONSHIP_DENY instead
     */
    const PLATFORM_RELATIONSHIP_DENY = 'deny';

    /**
     * @deprecated Use GoogleVideo::LIVE_YES instead
     */
    const LIVE_YES = 'yes';

    /**
     * @deprecated Use GoogleVideo::LIVE_NO instead
     */
    const LIVE_NO = 'no';

    /**
     * @deprecated Use GoogleVideo::TAG_ITEMS_LIMIT instead
     */
    const TAG_ITEMS_LIMIT = 32;

    private $video = null;

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
    public function __construct(Url $urlDecorated, $thumnail_loc = null, $title = null, $description = null, array $parameters = null)
    {
        parent::__construct($urlDecorated);

        if ($thumnail_loc !== null || $title !== null || $description !== null || $parameters !== null) {
            @trigger_error('Using other arguments than $urlDecorated in constructor is deprecated. Create a GoogleVideo object instead.', E_USER_DEPRECATED);

            $this->video = new GoogleVideo($thumnail_loc, $title, $description, $parameters ?: []);
        }
    }

    /**
     * Checker and deprecation triggerer for backward compatibility
     *
     * @param string $methodName
     */
    private function bc(string $methodName): void
    {
        switch (substr($methodName, 0, 3)) {
            case 'set':
                $text = 'Using %s::%s is deprecated. Create a GoogleVideo object instead.';
                break;
            case 'get':
                $text = 'Using %s::%s is deprecated. Retrieve it from GoogleVideo object instead.';
                break;
            case 'add':
                $text = 'Using %s::%s is deprecated. Add it to GoogleVideo object instead.';
                break;
            default:
                $text = 'Using %s::%s is deprecated.';
        }

        @trigger_error(
            sprintf($text, __CLASS__, $methodName),
            E_USER_DEPRECATED
        );

        if (!$this->video) {
            throw new Exception\GoogleVideoUrlException("thumnail_loc, title and description must be set");
        }
    }

    /**
     * @param string $thumbnail_loc
     *
     * @return GoogleVideoUrlDecorator
     *
     * @deprecated Using setThumbnailLoc is deprecated. Create a GoogleVideo object instead.
     */
    public function setThumbnailLoc($thumbnail_loc)
    {
        $this->bc(__FUNCTION__);

        $this->video->setThumbnailLocation($thumbnail_loc);

        return $this;
    }

    /**
     * @return string
     *
     * @deprecated Using getThumbnailLoc is deprecated. Retrieve it from GoogleVideo object instead.
     */
    public function getThumbnailLoc()
    {
        $this->bc(__FUNCTION__);

        return $this->video->getThumbnailLocation();
    }

    /**
     * @param string $title
     *
     * @return GoogleVideoUrlDecorator
     *
     * @deprecated Using setThumbnailLoc is deprecated. Create a GoogleVideo object instead.
     */
    public function setTitle($title)
    {
        $this->bc(__FUNCTION__);

        $this->video->setTitle($title);

        return $this;
    }

    /**
     * @param string $description
     *
     * @return GoogleVideoUrlDecorator
     *
     * @deprecated Using setThumbnailLoc is deprecated. Create a GoogleVideo object instead.
     */
    public function setDescription($description)
    {
        $this->bc(__FUNCTION__);

        $this->video->setDescription($description);

        return $this;
    }

    /**
     * @param string $content_loc
     *
     * @return GoogleVideoUrlDecorator
     *
     * @deprecated Using setThumbnailLoc is deprecated. Create a GoogleVideo object instead.
     */
    public function setContentLoc($content_loc)
    {
        $this->bc(__FUNCTION__);

        $this->video->setContentLocation($content_loc);

        return $this;
    }

    /**
     * @param string $player_loc
     *
     * @return GoogleVideoUrlDecorator
     *
     * @deprecated Using setThumbnailLoc is deprecated. Create a GoogleVideo object instead.
     */
    public function setPlayerLoc($player_loc)
    {
        $this->bc(__FUNCTION__);

        $this->video->setPlayerLocation($player_loc);

        return $this;
    }

    /**
     * @return string|null
     *
     * @deprecated Using getThumbnailLoc is deprecated. Retrieve it from GoogleVideo object instead.
     */
    public function getPlayerLoc()
    {
        $this->bc(__FUNCTION__);

        return $this->video->getPlayerLocation();
    }

    /**
     * @param string $player_loc_allow_embed
     *
     * @return GoogleVideoUrlDecorator
     *
     * @deprecated Using setThumbnailLoc is deprecated. Create a GoogleVideo object instead.
     */
    public function setPlayerLocAllowEmbed($player_loc_allow_embed)
    {
        $this->bc(__FUNCTION__);

        $this->video->setPlayerLocationAllowEmbed($player_loc_allow_embed);

        return $this;
    }

    /**
     * @return string
     *
     * @deprecated Using getThumbnailLoc is deprecated. Retrieve it from GoogleVideo object instead.
     */
    public function getPlayerLocAllowEmbed()
    {
        $this->bc(__FUNCTION__);

        return $this->video->getPlayerLocationAllowEmbed();
    }

    /**
     * @param string $player_loc_autoplay
     *
     * @return GoogleVideoUrlDecorator
     *
     * @deprecated Using setThumbnailLoc is deprecated. Create a GoogleVideo object instead.
     */
    public function setPlayerLocAutoplay($player_loc_autoplay)
    {
        $this->bc(__FUNCTION__);

        $this->video->setPlayerLocationAutoplay($player_loc_autoplay);

        return $this;
    }

    /**
     * @deprecated Using getThumbnailLoc is deprecated. Retrieve it from GoogleVideo object instead.
     */
    public function getPlayerLocAutoplay()
    {
        $this->bc(__FUNCTION__);

        return $this->video->getPlayerLocationAutoplay();
    }

    /**
     * @param int $duration
     *
     * @return GoogleVideoUrlDecorator
     * @throws Exception\GoogleVideoUrlException
     *
     * @deprecated Using setThumbnailLoc is deprecated. Create a GoogleVideo object instead.
     */
    public function setDuration($duration)
    {
        $this->bc(__FUNCTION__);

        $this->video->setDuration($duration);

        return $this;
    }

    /**
     * @param DateTimeInterface $expiration_date
     *
     * @return GoogleVideoUrlDecorator
     *
     * @deprecated Using setThumbnailLoc is deprecated. Create a GoogleVideo object instead.
     */
    public function setExpirationDate(DateTimeInterface $expiration_date)
    {
        $this->bc(__FUNCTION__);

        $this->video->setExpirationDate($expiration_date);

        return $this;
    }

    /**
     * @param float $rating
     *
     * @return GoogleVideoUrlDecorator
     *
     * @deprecated Using setThumbnailLoc is deprecated. Create a GoogleVideo object instead.
     */
    public function setRating($rating)
    {
        $this->bc(__FUNCTION__);

        $this->video->setRating($rating);

        return $this;
    }

    /**
     * @param int $view_count
     *
     * @return GoogleVideoUrlDecorator
     *
     * @deprecated Using setThumbnailLoc is deprecated. Create a GoogleVideo object instead.
     */
    public function setViewCount($view_count)
    {
        $this->bc(__FUNCTION__);

        $this->video->setViewCount($view_count);

        return $this;
    }

    /**
     * @param DateTimeInterface $publication_date
     *
     * @return GoogleVideoUrlDecorator
     *
     * @deprecated Using setThumbnailLoc is deprecated. Create a GoogleVideo object instead.
     */
    public function setPublicationDate(DateTimeInterface $publication_date)
    {
        $this->bc(__FUNCTION__);

        $this->video->setPublicationDate($publication_date);

        return $this;
    }

    /**
     * @param null|string $family_friendly
     *
     * @return GoogleVideoUrlDecorator
     *
     * @deprecated Using setThumbnailLoc is deprecated. Create a GoogleVideo object instead.
     */
    public function setFamilyFriendly($family_friendly = null)
    {
        $this->bc(__FUNCTION__);

        $this->video->setFamilyFriendly($family_friendly);

        return $this;
    }

    /**
     * @param string $category
     *
     * @return GoogleVideoUrlDecorator
     *
     * @deprecated Using setThumbnailLoc is deprecated. Create a GoogleVideo object instead.
     */
    public function setCategory($category)
    {
        $this->bc(__FUNCTION__);

        $this->video->setCategory($category);

        return $this;
    }

    /**
     * @param array $countries
     *
     * @return GoogleVideoUrlDecorator
     *
     * @deprecated Using setThumbnailLoc is deprecated. Create a GoogleVideo object instead.
     */
    public function setRestrictionAllow(array $countries)
    {
        $this->bc(__FUNCTION__);

        $this->video->setRestrictionAllow($countries);

        return $this;
    }

    /**
     * @return array
     *
     * @deprecated Using getThumbnailLoc is deprecated. Retrieve it from GoogleVideo object instead.
     */
    public function getRestrictionAllow()
    {
        $this->bc(__FUNCTION__);

        return $this->video->getRestrictionAllow();
    }

    /**
     * @param array $countries
     *
     * @return GoogleVideoUrlDecorator
     *
     * @deprecated Using setThumbnailLoc is deprecated. Create a GoogleVideo object instead.
     */
    public function setRestrictionDeny(array $countries)
    {
        $this->bc(__FUNCTION__);

        $this->video->setRestrictionDeny($countries);

        return $this;
    }

    /**
     * @return array
     *
     * @deprecated Using getThumbnailLoc is deprecated. Retrieve it from GoogleVideo object instead.
     */
    public function getRestrictionDeny()
    {
        $this->bc(__FUNCTION__);

        return $this->video->getRestrictionDeny();
    }

    /**
     * @param string $gallery_loc
     *
     * @return GoogleVideoUrlDecorator
     *
     * @deprecated Using setThumbnailLoc is deprecated. Create a GoogleVideo object instead.
     */
    public function setGalleryLoc($gallery_loc)
    {
        $this->bc(__FUNCTION__);

        $this->video->setGalleryLocation($gallery_loc);

        return $this;
    }

    /**
     * @param string $gallery_loc_title
     *
     * @return GoogleVideoUrlDecorator
     *
     * @deprecated Using setThumbnailLoc is deprecated. Create a GoogleVideo object instead.
     */
    public function setGalleryLocTitle($gallery_loc_title)
    {
        $this->bc(__FUNCTION__);

        $this->video->setGalleryLocationTitle($gallery_loc_title);

        return $this;
    }

    /**
     * @param string $requires_subscription
     *
     * @return GoogleVideoUrlDecorator
     *
     * @deprecated Using setThumbnailLoc is deprecated. Create a GoogleVideo object instead.
     */
    public function setRequiresSubscription($requires_subscription)
    {
        $this->bc(__FUNCTION__);

        $this->video->setRequiresSubscription($requires_subscription);

        return $this;
    }

    /**
     * @param string $uploader
     *
     * @return GoogleVideoUrlDecorator
     *
     * @deprecated Using setThumbnailLoc is deprecated. Create a GoogleVideo object instead.
     */
    public function setUploader($uploader)
    {
        $this->bc(__FUNCTION__);

        $this->video->setUploader($uploader);

        return $this;
    }

    /**
     * @param string $uploader_info
     *
     * @return GoogleVideoUrlDecorator
     *
     * @deprecated Using setThumbnailLoc is deprecated. Create a GoogleVideo object instead.
     */
    public function setUploaderInfo($uploader_info)
    {
        $this->bc(__FUNCTION__);

        $this->video->setUploaderInfo($uploader_info);

        return $this;
    }

    /**
     * @param array $platforms
     *
     * @return GoogleVideoUrlDecorator
     *
     * @deprecated Using setThumbnailLoc is deprecated. Create a GoogleVideo object instead.
     */
    public function setPlatforms(array $platforms)
    {
        $this->bc(__FUNCTION__);

        $this->video->setPlatforms($platforms);

        return $this;
    }

    /**
     * @return array
     *
     * @deprecated Using getThumbnailLoc is deprecated. Retrieve it from GoogleVideo object instead.
     */
    public function getPlatforms()
    {
        $this->bc(__FUNCTION__);

        return $this->video->getPlatforms();
    }

    /**
     * @param string $platform_relationship
     *
     * @return GoogleVideoUrlDecorator
     *
     * @deprecated Using setThumbnailLoc is deprecated. Create a GoogleVideo object instead.
     */
    public function setPlatformRelationship($platform_relationship)
    {
        $this->bc(__FUNCTION__);

        $this->video->setPlatformRelationship($platform_relationship);

        return $this;
    }

    /**
     * @return null|string
     *
     * @deprecated Using setThumbnailLoc is deprecated. Create a GoogleVideo object instead.
     */
    public function getPlatformRelationship()
    {
        $this->bc(__FUNCTION__);

        return $this->video->getPlatformRelationship();
    }

    /**
     * @param string $live
     *
     * @return GoogleVideoUrlDecorator
     *
     * @deprecated Using setThumbnailLoc is deprecated. Create a GoogleVideo object instead.
     */
    public function setLive($live)
    {
        $this->bc(__FUNCTION__);

        $this->video->setLive($live);

        return $this;
    }

    /**
     * @return string
     *
     * @deprecated Using getThumbnailLoc is deprecated. Retrieve it from GoogleVideo object instead.
     */
    public function getTitle()
    {
        $this->bc(__FUNCTION__);

        return $this->video->getTitle();
    }

    /**
     * @return string
     *
     * @deprecated Using getThumbnailLoc is deprecated. Retrieve it from GoogleVideo object instead.
     */
    public function getDescription()
    {
        $this->bc(__FUNCTION__);

        return $this->video->getDescription();
    }

    /**
     * @return null|string
     *
     * @deprecated Using getThumbnailLoc is deprecated. Retrieve it from GoogleVideo object instead.
     */
    public function getContentLoc()
    {
        $this->bc(__FUNCTION__);

        return $this->video->getContentLocation();
    }

    /**
     * @return int|null
     *
     * @deprecated Using getThumbnailLoc is deprecated. Retrieve it from GoogleVideo object instead.
     */
    public function getDuration()
    {
        $this->bc(__FUNCTION__);

        return $this->video->getDuration();
    }

    /**
     * @return DateTimeInterface|null
     *
     * @deprecated Using getThumbnailLoc is deprecated. Retrieve it from GoogleVideo object instead.
     */
    public function getExpirationDate()
    {
        $this->bc(__FUNCTION__);

        return $this->video->getExpirationDate();
    }

    /**
     * @return int|null
     *
     * @deprecated Using getThumbnailLoc is deprecated. Retrieve it from GoogleVideo object instead.
     */
    public function getRating()
    {
        $this->bc(__FUNCTION__);

        return $this->video->getRating();
    }

    /**
     * @return int|null
     *
     * @deprecated Using getThumbnailLoc is deprecated. Retrieve it from GoogleVideo object instead.
     */
    public function getViewCount()
    {
        $this->bc(__FUNCTION__);

        return $this->video->getViewCount();
    }

    /**
     * @return DateTimeInterface|null
     *
     * @deprecated Using getThumbnailLoc is deprecated. Retrieve it from GoogleVideo object instead.
     */
    public function getPublicationDate()
    {
        $this->bc(__FUNCTION__);

        return $this->video->getPublicationDate();
    }

    /**
     * @return null|string
     *
     * @deprecated Using getThumbnailLoc is deprecated. Retrieve it from GoogleVideo object instead.
     */
    public function getFamilyFriendly()
    {
        $this->bc(__FUNCTION__);

        return $this->video->getFamilyFriendly();
    }

    /**
     * @return null|string
     *
     * @deprecated Using getThumbnailLoc is deprecated. Retrieve it from GoogleVideo object instead.
     */
    public function getCategory()
    {
        $this->bc(__FUNCTION__);

        return $this->video->getCategory();
    }

    /**
     * @return null|string
     *
     * @deprecated Using getThumbnailLoc is deprecated. Retrieve it from GoogleVideo object instead.
     */
    public function getGalleryLoc()
    {
        $this->bc(__FUNCTION__);

        return $this->video->getGalleryLocation();
    }

    /**
     * @return null|string
     *
     * @deprecated Using getThumbnailLoc is deprecated. Retrieve it from GoogleVideo object instead.
     */
    public function getGalleryLocTitle()
    {
        $this->bc(__FUNCTION__);

        return $this->video->getGalleryLocationTitle();
    }

    /**
     * @return null|string
     *
     * @deprecated Using getThumbnailLoc is deprecated. Retrieve it from GoogleVideo object instead.
     */
    public function getRequiresSubscription()
    {
        $this->bc(__FUNCTION__);

        return $this->video->getRequiresSubscription();
    }

    /**
     * @return null|string
     *
     * @deprecated Using getThumbnailLoc is deprecated. Retrieve it from GoogleVideo object instead.
     */
    public function getUploader()
    {
        $this->bc(__FUNCTION__);

        return $this->video->getUploader();
    }

    /**
     * @return null|string
     *
     * @deprecated Using getThumbnailLoc is deprecated. Retrieve it from GoogleVideo object instead.
     */
    public function getUploaderInfo()
    {
        $this->bc(__FUNCTION__);

        return $this->video->getUploaderInfo();
    }

    /**
     * @return string|null
     *
     * @deprecated Using getThumbnailLoc is deprecated. Retrieve it from GoogleVideo object instead.
     */
    public function getLive()
    {
        $this->bc(__FUNCTION__);

        return $this->video->getLive();
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
     *
     * @deprecated Using setThumbnailLoc is deprecated. Add it to GoogleVideo object instead.
     */
    public function addPrice($amount, $currency, $type = null, $resolution = null)
    {
        $this->bc(__FUNCTION__);

        $this->video->addPrice($amount, $currency, $type, $resolution);

        return $this;
    }

    /**
     * list of defined prices with price, currency, type and resolution
     *
     * @return array
     *
     * @deprecated Using getThumbnailLoc is deprecated. Retrieve it from GoogleVideo object instead.
     */
    public function getPrices()
    {
        $this->bc(__FUNCTION__);

        return $this->video->getPrices();
    }

    /**
     * @param string $tag
     *
     * @return GoogleVideoUrlDecorator
     * @throws Exception\GoogleVideoUrlTagException
     *
     * @deprecated Using setThumbnailLoc is deprecated. Add it to GoogleVideo object instead.
     */
    public function addTag($tag)
    {
        $this->bc(__FUNCTION__);

        $this->video->addTag($tag);

        return $this;
    }

    /**
     * @return array
     *
     * @deprecated Using getThumbnailLoc is deprecated. Retrieve it from GoogleVideo object instead.
     */
    public function getTags()
    {
        $this->bc(__FUNCTION__);

        return $this->video->getTags();
    }
}
