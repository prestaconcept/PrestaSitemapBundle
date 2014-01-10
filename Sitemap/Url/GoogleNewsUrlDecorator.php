<?php

/**
 * This file is part of the PrestaSitemapBundle
 *
 * (c) PrestaConcept <www.prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle\Sitemap\Url;

use Presta\SitemapBundle\Exception;
use Presta\SitemapBundle\Sitemap\Utils;

/**
 * Helps to generate google news urls
 *
 * @see guidelines at https://support.google.com/webmasters/answer/74288
 *
 * @author Christoph Foehrdes
 */
class GoogleNewsUrlDecorator extends UrlDecorator
{
    const ACCESS_SUBSCRIPTION = 'Subscription';
    const ACCESS_REGISTRATION = 'Registration';

    const DATE_FORMAT_DATE = 'Y-m-d';
    const DATE_FORMAT_DATE_TIME = \DateTime::W3C;

    /**
     * @var array $customNamespaces
     */
    protected $customNamespaces = array('news' => 'http://www.google.com/schemas/sitemap-news/0.9');

    /**
     * @var string $publicationName
     */
    private $publicationName;

    /**
     * @var string $publicationLanguage
     */
    private $publicationLanguage;

    /**
     * @var string $access
     */
    private $access;

    /**
     * @var array $genres
     */
    private $genres;

    /**
     * @var \DateTime $publicationDate
     */
    private $publicationDate;

    /**
     * @var string $publicationDateFormat
     */
    private $publicationDateFormat = self::DATE_FORMAT_DATE_TIME;

    /**
     * @var string $title
     */
    private $title;

    /**
     * @var string $geoLocations
     */
    private $geoLocations;

    /**
     * @var array $keywords
     */
    private $keywords = array();

    /**
     * @var array $stockTickers
     */
    private $stockTickers = array();

    /**
     * @param Url       $urlDecorated
     * @param string    $publicationName
     * @param string    $publicationLanguage
     * @param \DateTime $publicationDate
     * @param string    $title
     *
     * @throws Exception\GoogleNewsUrlException
     */
    public function __construct(Url $urlDecorated, $publicationName, $publicationLanguage, \DateTime $publicationDate, $title)
    {
        parent::__construct($urlDecorated);

        $this->publicationName = $publicationName;
        if (strlen($publicationLanguage) > 5) {
            throw new Exception\GoogleNewsUrlException('Use a 2 oder 3 character long ISO 639 language code. Except for chinese use zh-cn or zh-tw. See https://support.google.com/webmasters/answer/74288?hl=en&ref_topic=10078');
        }
        $this->publicationLanguage = $publicationLanguage;
        $this->publicationDate = $publicationDate;
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getPublicationName()
    {
        return $this->publicationName;
    }

    /**
     * @param string $publicationName
     */
    public function setPublicationName($publicationName)
    {
        $this->publicationName = $publicationName;
    }

    /**
     * @return string
     */
    public function getPublicationLanguage()
    {
        return $this->publicationLanguage;
    }

    /**
     * @param string $publicationLanguage
     */
    public function setPublicationLanguage($publicationLanguage)
    {
        $this->publicationLanguage = $publicationLanguage;
    }

    /**
     * @return string
     */
    public function getAccess()
    {
        return $this->access;
    }

    /**
     * @param string $access
     *
     * @throws Exception\GoogleNewsUrlException
     */
    public function setAccess($access)
    {
        if ($access && !in_array($access, array(self::ACCESS_REGISTRATION, self::ACCESS_SUBSCRIPTION))) {
            throw new Exception\GoogleNewsUrlException(sprintf('The parameter %s must be a valid access. See https://support.google.com/webmasters/answer/74288?hl=en&ref_topic=10078', $access));
        }
        $this->access = $access;
    }

    /**
     * @return array
     */
    public function getGenres()
    {
        return $this->genres;
    }

    /**
     * @param array $genres
     */
    public function setGenres($genres)
    {
        $this->genres = $genres;
    }

    /**
     * @param string $genre
     */
    public function addGenre($genre)
    {
        $this->genres[] = $genre;
    }

    /**
     * @return \DateTime
     */
    public function getPublicationDate()
    {
        return $this->publicationDate;
    }

    /**
     * @param \DateTime $publicationDate
     */
    public function setPublicationDate($publicationDate)
    {
        $this->publicationDate = $publicationDate;
    }

    /**
     * @return string
     */
    public function getPublicationDateFormat()
    {
        return $this->publicationDateFormat;
    }

    /**
     * @param string $publicationDateFormat
     *
     * @throws Exception\GoogleNewsUrlException
     */
    public function setPublicationDateFormat($publicationDateFormat)
    {
        if ($publicationDateFormat && !in_array($publicationDateFormat, array(self::DATE_FORMAT_DATE, self::DATE_FORMAT_DATE_TIME))) {
            throw new Exception\GoogleNewsUrlException(sprintf('The parameter %s must be a valid date format. See https://support.google.com/webmasters/answer/74288?hl=en', $publicationDateFormat));
        }
        $this->publicationDateFormat = $publicationDateFormat;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getGeoLocations()
    {
        return $this->geoLocations;
    }

    /**
     * @param string $geoLocations
     *
     * @throws Exception\GoogleNewsUrlException
     */
    public function setGeoLocations($geoLocations)
    {
        $locationParts = explode(', ', $geoLocations);
        if (count($locationParts) < 2) {
            throw new Exception\GoogleNewsUrlException(sprintf('The parameter %s must be a valid geo_location. See https://support.google.com/news/publisher/answer/1662970?hl=en', $geoLocations));
        }
        $this->geoLocations = $geoLocations;
    }

    /**
     * @return array
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * @param array $keywords
     */
    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;
    }

    /**
     * @param string $keyword
     */
    public function addKeyword($keyword)
    {
        $this->keywords[] = $keyword;
    }

    /**
     * @return array
     */
    public function getStockTickers()
    {
        return $this->stockTickers;
    }

    /**
     * @param array $stockTickers
     *
     * @throws Exception\GoogleNewsUrlException If the stock ticker limit is reached
     */
    public function setStockTickers($stockTickers)
    {
        if ($stockTickers && count($stockTickers) > 5) {
            throw new Exception\GoogleNewsUrlException('The stock tickers are limited to 5. See https://support.google.com/webmasters/answer/74288?hl=en&ref_topic=10078');
        }
        $this->stockTickers = $stockTickers;
    }

    /**
     * @param string $stockTicker
     *
     * @throws Exception\GoogleNewsUrlException If the stock ticker limit is reached
     */
    public function addStockTicker($stockTicker)
    {
        if ($this->stockTickers && count($this->stockTickers) == 5) {
            throw new Exception\GoogleNewsUrlException('The stock tickers are limited to 5. See https://support.google.com/webmasters/answer/74288?hl=en&ref_topic=10078');
        }
        $this->stockTickers[] = $stockTicker;
    }

    /**
     * {@inheritdoc}
     */
    public function toXml()
    {
        $newsXml = '<news:news>';

        $newsXml .= '<news:publication>';
        $newsXml .= '<news:name>' . Utils::render($this->getPublicationName()) . '</news:name>';
        $newsXml .= '<news:language>' . $this->getPublicationLanguage() . '</news:language>';
        $newsXml .= '</news:publication>';

        if ($this->getAccess()) {
            $newsXml .= '<news:access>' . $this->getAccess() . '</news:access>';
        }

        if ($this->getGenres() && count($this->getGenres()) > 0) {
            $newsXml .= '<news:genres>' . implode(', ', $this->getGenres()) . '</news:genres>';
        }

        $newsXml .= '<news:publication_date>' . $this->getPublicationDate()->format($this->getPublicationDateFormat()) . '</news:publication_date>';

        $newsXml .= '<news:title>' . Utils::render($this->getTitle()) . '</news:title>';

        if ($this->getGeoLocations()) {
            $newsXml .= '<news:geo_locations>' . $this->getGeoLocations() . '</news:geo_locations>';
        }

        if ($this->getKeywords() && count($this->getKeywords()) > 0) {
            $newsXml .= '<news:keywords>' . implode(', ', $this->getKeywords()) . '</news:keywords>';
        }

        if ($this->getStockTickers() && count($this->getStockTickers()) > 0) {
            $newsXml .= '<news:stock_tickers>' . implode(', ', $this->getStockTickers()) . '</news:stock_tickers>';
        }

        $newsXml .= '</news:news>';

        $baseXml = $this->urlDecorated->toXml();

        return str_replace('</url>', $newsXml . '</url>', $baseXml);
    }
}
