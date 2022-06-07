<?php

/*
 * This file is part of the PrestaSitemapBundle package.
 *
 * (c) PrestaConcept <https://prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle\Sitemap\Url;

use DateTimeInterface;
use Presta\SitemapBundle\Exception;
use Presta\SitemapBundle\Sitemap\Utils;

/**
 * Sitemap video object attached to a sitemap Url.
 *
 * https://developers.google.com/search/docs/advanced/sitemaps/video-sitemaps
 */
class GoogleVideo
{
    public const PLAYER_LOC_ALLOW_EMBED_YES = 'yes';
    public const PLAYER_LOC_ALLOW_EMBED_NO = 'no';
    public const FAMILY_FRIENDLY_YES = 'yes';
    public const FAMILY_FRIENDLY_NO = 'no';
    public const RELATIONSHIP_ALLOW = 'allow';
    public const RELATIONSHIP_DENY = 'deny';
    public const PRICE_TYPE_RENT = 'rent';
    public const PRICE_TYPE_OWN = 'own';
    public const PRICE_RESOLUTION_HD = 'HD';
    public const PRICE_RESOLUTION_SD = 'SD';
    public const REQUIRES_SUBSCRIPTION_YES = 'yes';
    public const REQUIRES_SUBSCRIPTION_NO = 'no';
    public const PLATFORM_WEB = 'web';
    public const PLATFORM_MOBILE = 'mobile';
    public const PLATFORM_TV = 'tv';
    public const PLATFORM_RELATIONSHIP_ALLOW = 'allow';
    public const PLATFORM_RELATIONSHIP_DENY = 'deny';
    public const LIVE_YES = 'yes';
    public const LIVE_NO = 'no';
    public const TAG_ITEMS_LIMIT = 32;

    /**
     * @var string
     */
    protected $thumbnailLocation;

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
    protected $contentLocation;

    /**
     * @var string|null
     */
    protected $playerLocation;

    /**
     * allow google to embed video in search results
     * @var string|null
     */
    protected $playerLocationAllowEmbed;

    /**
     * user defined string for flashvar parameters in embed tag (e.g. autoplay="ap=1")
     * @var string|null
     */
    protected $playerLocationAutoplay;

    /**
     * @var int|null
     */
    protected $duration;

    /**
     * @var DateTimeInterface|null
     */
    protected $expirationDate;

    /**
     * @var int|float|null
     */
    protected $rating;

    /**
     * @var int|null
     */
    protected $viewCount;

    /**
     * @var DateTimeInterface|null
     */
    protected $publicationDate;

    /**
     * @var string|null
     */
    protected $familyFriendly;

    /**
     * @var string|null
     */
    protected $category;

    /**
     * @var array<int, string>
     */
    protected $restrictionAllow = [];

    /**
     * @var array<int, string>
     */
    protected $restrictionDeny = [];

    /**
     * @var string|null
     */
    protected $galleryLocation;

    /**
     * @var string|null
     */
    protected $galleryLocationTitle;

    /**
     * @var string|null
     */
    protected $requiresSubscription;

    /**
     * @var string|null
     */
    protected $uploader;

    /**
     * @var string|null
     */
    protected $uploaderInfo;

    /**
     * @var array<int, string>
     */
    protected $platforms = [];

    /**
     * @var string|null
     */
    protected $platformRelationship;

    /**
     * @var string|null
     */
    protected $live;

    /**
     * multiple prices can be added, see self::addPrice()
     * @var array<int, array<string, mixed>>
     */
    protected $prices = [];

    /**
     * multiple tags can be added, see self::addTag()
     * @var array<int, string>
     */
    protected $tags = [];

    /**
     * create a GoogleImage for your GoogleImageUrl
     *
     * @param string               $thumbnailLocation
     * @param string               $title
     * @param string               $description
     * @param array{
     *     content_location?: string,
     *     player_location?: string,
     *     player_location_allow_embed?: string,
     *     player_location_autoplay?: string,
     *     duration?: int,
     *     expiration_date?: DateTimeInterface,
     *     rating?: float|int,
     *     view_count?: int,
     *     publication_date?: DateTimeInterface,
     *     family_friendly?: string,
     *     category?: string,
     *     restriction_allow?: array<int, string>,
     *     restriction_deny?: array<int, string>,
     *     gallery_location?: string,
     *     gallery_location_title?: string,
     *     requires_subscription?: string,
     *     uploader?: string,
     *     uploader_info?: string,
     *     platforms?: array<int, string>,
     *     platform_relationship?: string,
     *     live?: string,
     * } $parameters
     *
     * @throws Exception\GoogleVideoException
     */
    public function __construct(string $thumbnailLocation, string $title, string $description, array $parameters = [])
    {
        foreach ($parameters as $key => $param) {
            switch ($key) {
                case 'content_location':
                    /** @var string $param */
                    $this->setContentLocation($param);
                    break;
                case 'player_location':
                    /** @var string $param */
                    $this->setPlayerLocation($param);
                    break;
                case 'player_location_allow_embed':
                    /** @var string $param */
                    $this->setPlayerLocationAllowEmbed($param);
                    break;
                case 'player_location_autoplay':
                    /** @var string $param */
                    $this->setPlayerLocationAutoplay($param);
                    break;
                case 'duration':
                    /** @var int $param */
                    $this->setDuration($param);
                    break;
                case 'expiration_date':
                    /** @var DateTimeInterface $param */
                    $this->setExpirationDate($param);
                    break;
                case 'rating':
                    /** @var float|int $param */
                    $this->setRating($param);
                    break;
                case 'view_count':
                    /** @var int $param */
                    $this->setViewCount($param);
                    break;
                case 'publication_date':
                    /** @var DateTimeInterface $param */
                    $this->setPublicationDate($param);
                    break;
                case 'family_friendly':
                    /** @var string $param */
                    $this->setFamilyFriendly($param);
                    break;
                case 'category':
                    /** @var string $param */
                    $this->setCategory($param);
                    break;
                case 'restriction_allow':
                    /** @var array<int, string> $param */
                    $this->setRestrictionAllow($param);
                    break;
                case 'restriction_deny':
                    /** @var array<int, string> $param */
                    $this->setRestrictionDeny($param);
                    break;
                case 'gallery_location':
                    /** @var string $param */
                    $this->setGalleryLocation($param);
                    break;
                case 'gallery_location_title':
                    /** @var string $param */
                    $this->setGalleryLocationTitle($param);
                    break;
                case 'requires_subscription':
                    /** @var string $param */
                    $this->setRequiresSubscription($param);
                    break;
                case 'uploader':
                    /** @var string $param */
                    $this->setUploader($param);
                    break;
                case 'uploader_info':
                    /** @var string $param */
                    $this->setUploaderInfo($param);
                    break;
                case 'platforms':
                    /** @var array<int, string> $param */
                    $this->setPlatforms($param);
                    break;
                case 'platform_relationship':
                    /** @var string $param */
                    $this->setPlatformRelationship($param);
                    break;
                case 'live':
                    /** @var string $param */
                    $this->setLive($param);
                    break;
            }
        }

        $this->setThumbnailLocation($thumbnailLocation);
        $this->setTitle($title);
        $this->setDescription($description);

        if (!$this->contentLocation && !$this->playerLocation) {
            throw new Exception\GoogleVideoException('The parameter content_location or content_location is required');
        }

        if (count($this->platforms) && !$this->platformRelationship) {
            throw new Exception\GoogleVideoException(
                'The parameter platform_relationship is required when platform is set'
            );
        }
    }

    /**
     * @param string $location
     *
     * @return GoogleVideo
     */
    public function setThumbnailLocation(string $location): self
    {
        $this->thumbnailLocation = $location;

        return $this;
    }

    /**
     * @return string
     */
    public function getThumbnailLocation(): string
    {
        return $this->thumbnailLocation;
    }

    /**
     * @param string $title
     *
     * @return GoogleVideo
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @param string $description
     *
     * @return GoogleVideo
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @param string $location
     *
     * @return GoogleVideo
     */
    public function setContentLocation(string $location): self
    {
        $this->contentLocation = $location;

        return $this;
    }

    /**
     * @param string $location
     *
     * @return GoogleVideo
     */
    public function setPlayerLocation(string $location): self
    {
        $this->playerLocation = $location;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPlayerLocation(): ?string
    {
        return $this->playerLocation;
    }

    /**
     * @param string|null $embed
     *
     * @return GoogleVideo
     */
    public function setPlayerLocationAllowEmbed(?string $embed): self
    {
        if ($embed && !in_array($embed, [self::PLAYER_LOC_ALLOW_EMBED_YES, self::PLAYER_LOC_ALLOW_EMBED_NO])) {
            throw new Exception\GoogleVideoException(
                sprintf(
                    'The parameter %s must be a valid player_location_allow_embed.' .
                    ' See http://support.google.com/webmasters/bin/answer.py?hl=en&answer=80472#4',
                    $embed
                )
            );
        }
        $this->playerLocationAllowEmbed = $embed;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPlayerLocationAllowEmbed(): ?string
    {
        return $this->playerLocationAllowEmbed;
    }

    /**
     * @param string|null $autoplay
     *
     * @return GoogleVideo
     */
    public function setPlayerLocationAutoplay(?string $autoplay): self
    {
        $this->playerLocationAutoplay = $autoplay;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPlayerLocationAutoplay(): ?string
    {
        return $this->playerLocationAutoplay;
    }

    /**
     * @param int $duration
     *
     * @return GoogleVideo
     * @throws Exception\GoogleVideoException
     */
    public function setDuration(int $duration): self
    {
        if ($duration < 0 || $duration > 28800) {
            throw new Exception\GoogleVideoException(
                sprintf(
                    'The parameter %s must be a valid duration.' .
                    ' See http://support.google.com/webmasters/bin/answer.py?hl=en&answer=80472#4',
                    $duration
                )
            );
        }

        $this->duration = $duration;

        return $this;
    }

    /**
     * @param DateTimeInterface $expirationDate
     *
     * @return GoogleVideo
     */
    public function setExpirationDate(DateTimeInterface $expirationDate): self
    {
        $this->expirationDate = $expirationDate;

        return $this;
    }

    /**
     * @param float|int $rating
     *
     * @return GoogleVideo
     */
    public function setRating($rating): self
    {
        if ($rating < 0 || $rating > 5) {
            throw new Exception\GoogleVideoException(
                sprintf(
                    'The parameter %s must be a valid rating.' .
                    ' See http://support.google.com/webmasters/bin/answer.py?hl=en&answer=80472#4',
                    $rating
                )
            );
        }

        $this->rating = $rating;

        return $this;
    }

    /**
     * @param int $viewCount
     *
     * @return GoogleVideo
     */
    public function setViewCount(int $viewCount): self
    {
        $this->viewCount = $viewCount;

        return $this;
    }

    /**
     * @param DateTimeInterface $publicationDate
     *
     * @return GoogleVideo
     */
    public function setPublicationDate(DateTimeInterface $publicationDate): self
    {
        $this->publicationDate = $publicationDate;

        return $this;
    }

    /**
     * @param null|string $familyFriendly
     *
     * @return GoogleVideo
     */
    public function setFamilyFriendly(string $familyFriendly = null): self
    {
        if (null == $familyFriendly) {
            $familyFriendly = self::FAMILY_FRIENDLY_YES;
        }

        if (!in_array($familyFriendly, [self::FAMILY_FRIENDLY_YES, self::FAMILY_FRIENDLY_NO])) {
            throw new Exception\GoogleVideoException(
                sprintf(
                    'The parameter %s must be a valid family_friendly.' .
                    ' See http://support.google.com/webmasters/bin/answer.py?hl=en&answer=80472#4',
                    $familyFriendly
                )
            );
        }

        $this->familyFriendly = $familyFriendly;

        return $this;
    }

    /**
     * @param string $category
     *
     * @return GoogleVideo
     */
    public function setCategory(string $category): self
    {
        if (strlen($category) > 256) {
            throw new Exception\GoogleVideoException(
                sprintf(
                    'The parameter %s must be a valid category.' .
                    ' See http://support.google.com/webmasters/bin/answer.py?hl=en&answer=80472#4',
                    $category
                )
            );
        }

        $this->category = $category;

        return $this;
    }

    /**
     * @param array<int, string> $countries
     *
     * @return GoogleVideo
     */
    public function setRestrictionAllow(array $countries): self
    {
        $this->restrictionAllow = $countries;

        return $this;
    }

    /**
     * @return array<int, string>
     */
    public function getRestrictionAllow()
    {
        return $this->restrictionAllow;
    }

    /**
     * @param array<int, string> $countries
     *
     * @return GoogleVideo
     */
    public function setRestrictionDeny(array $countries): self
    {
        $this->restrictionDeny = $countries;

        return $this;
    }

    /**
     * @return array<int, string>
     */
    public function getRestrictionDeny(): array
    {
        return $this->restrictionDeny;
    }

    /**
     * @param string $location
     *
     * @return GoogleVideo
     */
    public function setGalleryLocation(string $location): self
    {
        $this->galleryLocation = $location;

        return $this;
    }

    /**
     * @param string $title
     *
     * @return GoogleVideo
     */
    public function setGalleryLocationTitle(string $title): self
    {
        $this->galleryLocationTitle = $title;

        return $this;
    }

    /**
     * @param string $requiresSubscription
     *
     * @return GoogleVideo
     */
    public function setRequiresSubscription(string $requiresSubscription): self
    {
        if (!in_array($requiresSubscription, [self::REQUIRES_SUBSCRIPTION_YES, self::REQUIRES_SUBSCRIPTION_NO])) {
            throw new Exception\GoogleVideoException(
                sprintf(
                    'The parameter %s must be a valid requires_subscription.' .
                    ' See http://support.google.com/webmasters/bin/answer.py?hl=en&answer=80472#4',
                    $requiresSubscription
                )
            );
        }

        $this->requiresSubscription = $requiresSubscription;

        return $this;
    }

    /**
     * @param string $uploader
     *
     * @return GoogleVideo
     */
    public function setUploader(string $uploader): self
    {
        $this->uploader = $uploader;

        return $this;
    }

    /**
     * @param string $uploaderInfo
     *
     * @return GoogleVideo
     */
    public function setUploaderInfo(string $uploaderInfo): self
    {
        $this->uploaderInfo = $uploaderInfo;

        return $this;
    }

    /**
     * @param array<int, string> $platforms
     *
     * @return GoogleVideo
     */
    public function setPlatforms(array $platforms): self
    {
        $this->platforms = $platforms;

        return $this;
    }

    /**
     * @return array<int, string>
     */
    public function getPlatforms(): array
    {
        return $this->platforms;
    }

    /**
     * @param string $platformRelationship
     *
     * @return GoogleVideo
     */
    public function setPlatformRelationship(string $platformRelationship): self
    {
        $this->platformRelationship = $platformRelationship;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getPlatformRelationship(): ?string
    {
        return $this->platformRelationship;
    }

    /**
     * @param string $live
     *
     * @return GoogleVideo
     */
    public function setLive(string $live): self
    {
        if (!in_array($live, [self::LIVE_YES, self::LIVE_NO])) {
            throw new Exception\GoogleVideoException(
                sprintf(
                    'The parameter %s must be a valid live.' .
                    ' See http://support.google.com/webmasters/bin/answer.py?hl=en&answer=80472#4',
                    $live
                )
            );
        }

        $this->live = $live;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return null|string
     */
    public function getContentLocation(): ?string
    {
        return $this->contentLocation;
    }

    /**
     * @return int|null
     */
    public function getDuration(): ?int
    {
        return $this->duration;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getExpirationDate(): ?DateTimeInterface
    {
        return $this->expirationDate;
    }

    /**
     * @return int|float|null
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * @return int|null
     */
    public function getViewCount(): ?int
    {
        return $this->viewCount;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getPublicationDate(): ?DateTimeInterface
    {
        return $this->publicationDate;
    }

    /**
     * @return null|string
     */
    public function getFamilyFriendly(): ?string
    {
        return $this->familyFriendly;
    }

    /**
     * @return null|string
     */
    public function getCategory(): ?string
    {
        return $this->category;
    }

    /**
     * @return null|string
     */
    public function getGalleryLocation(): ?string
    {
        return $this->galleryLocation;
    }

    /**
     * @return null|string
     */
    public function getGalleryLocationTitle(): ?string
    {
        return $this->galleryLocationTitle;
    }

    /**
     * @return null|string
     */
    public function getRequiresSubscription(): ?string
    {
        return $this->requiresSubscription;
    }

    /**
     * @return null|string
     */
    public function getUploader(): ?string
    {
        return $this->uploader;
    }

    /**
     * @return null|string
     */
    public function getUploaderInfo(): ?string
    {
        return $this->uploaderInfo;
    }

    /**
     * @return string|null
     */
    public function getLive(): ?string
    {
        return $this->live;
    }

    /**
     * add price element
     *
     * @param int|float   $amount
     * @param string      $currency   - ISO 4217 format.
     * @param string|null $type       - rent or own
     * @param string|null $resolution - hd or sd
     *
     * @return GoogleVideo
     */
    public function addPrice($amount, string $currency, string $type = null, string $resolution = null): self
    {
        $this->prices[] = [
            'amount' => $amount,
            'currency' => $currency,
            'type' => $type,
            'resolution' => $resolution,
        ];

        return $this;
    }

    /**
     * list of defined prices with price, currency, type and resolution
     *
     * @return array<int, array<string, mixed>>
     */
    public function getPrices(): array
    {
        return $this->prices;
    }

    /**
     * @param string $tag
     *
     * @return GoogleVideo
     * @throws Exception\GoogleVideoTagException
     */
    public function addTag(string $tag): self
    {
        if (count($this->tags) >= self::TAG_ITEMS_LIMIT) {
            throw new Exception\GoogleVideoTagException(
                sprintf('The tags limit of %d items is exceeded.', self::TAG_ITEMS_LIMIT)
            );
        }

        $this->tags[] = $tag;

        return $this;
    }

    /**
     * @return array<int, string>
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * Return the xml representation for the video
     *
     * @return string
     */
    public function toXml(): string
    {
        $videoXml = '<video:video>';

        //----------------------
        // required fields
        $videoXml .= '<video:thumbnail_loc>' . Utils::encode($this->getThumbnailLocation()) . '</video:thumbnail_loc>';
        $videoXml .= '<video:title>' . Utils::cdata($this->getTitle()) . '</video:title>';
        $videoXml .= '<video:description>' . Utils::cdata($this->getDescription()) . '</video:description>';

        //----------------------
        //----------------------
        // simple optional fields
        if ($category = $this->getCategory()) {
            $videoXml .= '<video:category>' . Utils::cdata($category) . '</video:category>';
        }
        if ($location = $this->getContentLocation()) {
            $videoXml .= '<video:content_loc>' . Utils::encode($location) . '</video:content_loc>';
        }
        if ($duration = $this->getDuration()) {
            $videoXml .= '<video:duration>' . $duration . '</video:duration>';
        }
        if ($rating = $this->getRating()) {
            $videoXml .= '<video:rating>' . $rating . '</video:rating>';
        }
        if ($viewCount = $this->getViewCount()) {
            $videoXml .= '<video:view_count>' . $viewCount . '</video:view_count>';
        }
        if ($familyFriendly = $this->getFamilyFriendly()) {
            $videoXml .= '<video:family_friendly>' . $familyFriendly . '</video:family_friendly>';
        }
        if ($requiresSubscription = $this->getRequiresSubscription()) {
            $videoXml .= '<video:requires_subscription>' . $requiresSubscription . '</video:requires_subscription>';
        }
        if ($live = $this->getLive()) {
            $videoXml .= '<video:live>' . $live . '</video:live>';
        }

        //----------------------
        //----------------------
        // date based optional fields
        if ($date = $this->getExpirationDate()) {
            $videoXml .= '<video:expiration_date>' . $date->format('c') . '</video:expiration_date>';
        }
        if ($date = $this->getPublicationDate()) {
            $videoXml .= '<video:publication_date>' . $date->format('c') . '</video:publication_date>';
        }

        //----------------------
        //----------------------
        // more complex optional fields
        if ($playerLocation = $this->getPlayerLocation()) {
            $attributes = [];
            if ($uploaderInfo = $this->getPlayerLocationAllowEmbed()) {
                $attributes['allow_embed'] = Utils::encode($uploaderInfo);
            }
            if (null !== $autoplay = $this->getPlayerLocationAutoplay()) {
                $attributes['autoplay'] = Utils::encode($autoplay);
            }

            $videoXml .= '<video:player_loc' . $this->attributes($attributes) . '>'
                . Utils::encode($playerLocation)
                . '</video:player_loc>';
        }

        if ($allow = $this->getRestrictionAllow()) {
            $videoXml .= '<video:restriction relationship="allow">' . implode(' ', $allow) . '</video:restriction>';
        }

        if ($deny = $this->getRestrictionDeny()) {
            $videoXml .= '<video:restriction relationship="deny">' . implode(' ', $deny) . '</video:restriction>';
        }

        if ($galleryLocation = $this->getGalleryLocation()) {
            $attributes = [];
            if ($galleryLocationTitle = $this->getGalleryLocationTitle()) {
                $attributes['title'] = Utils::encode($galleryLocationTitle);
            }

            $videoXml .= '<video:gallery_loc' . $this->attributes($attributes) . '>'
                . Utils::encode($galleryLocation)
                . '</video:gallery_loc>';
        }

        foreach ($this->getTags() as $tag) {
            $videoXml .= '<video:tag>' . Utils::cdata($tag) . '</video:tag>';
        }

        foreach ($this->getPrices() as $price) {
            /** @var array<string, string> $attributes */
            $attributes = array_intersect_key($price, array_flip(['currency', 'type', 'resolution']));
            $attributes = array_filter($attributes);

            $videoXml .= '<video:price' . $this->attributes($attributes) . '>' . $price['amount'] . '</video:price>';
        }

        if ($uploader = $this->getUploader()) {
            $attributes = [];
            if ($uploaderInfo = $this->getUploaderInfo()) {
                $attributes['info'] = $uploaderInfo;
            }

            $videoXml .= '<video:uploader' . $this->attributes($attributes) . '>' . $uploader . '</video:uploader>';
        }

        if (count($platforms = $this->getPlatforms())) {
            $videoXml .= '<video:platform relationship="' . $this->getPlatformRelationship() . '">'
                . implode(' ', $platforms)
                . '</video:platform>';
        }
        //----------------------

        $videoXml .= '</video:video>';

        return $videoXml;
    }

    /**
     * @param array<string, string> $map
     *
     * @return string
     */
    private function attributes(array $map): string
    {
        $attributes = '';
        if (\count($map) === 0) {
            return $attributes;
        }

        foreach ($map as $name => $value) {
            $attributes .= ' ' . $name . '="' . $value . '"';
        }

        return ' ' . trim($attributes);
    }
}
