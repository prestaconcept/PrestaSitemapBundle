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
     * @var array
     */
    protected $customNamespaces = ['news' => 'http://www.google.com/schemas/sitemap-news/0.9'];

    /**
     * @var string
     */
    private $publicationName;

    /**
     * @var string
     */
    private $publicationLanguage;

    /**
     * @var string|null
     */
    private $access;

    /**
     * @var array
     */
    private $genres = [];

    /**
     * @var \DateTime
     */
    private $publicationDate;

    /**
     * @var string
     */
    private $publicationDateFormat = self::DATE_FORMAT_DATE_TIME;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string|null
     */
    private $geoLocations;

    /**
     * @var array
     */
    private $keywords = [];

    /**
     * @var array
     */
    private $stockTickers = [];

    /**
     * @param Url       $urlDecorated
     * @param string    $publicationName
     * @param string    $publicationLanguage
     * @param \DateTime $publicationDate
     * @param string    $title
     *
     * @throws Exception\GoogleNewsUrlException
     */
    public function __construct(
        Url $urlDecorated,
        string $publicationName,
        string $publicationLanguage,
        \DateTime $publicationDate,
        string $title
    ) {
        parent::__construct($urlDecorated);

        $this->publicationName = $publicationName;
        if (strlen($publicationLanguage) > 5) {
            throw new Exception\GoogleNewsUrlException(
                'Use a 2 oder 3 character long ISO 639 language code. Except for chinese use zh-cn or zh-tw.' .
                'See https://support.google.com/webmasters/answer/74288?hl=en&ref_topic=10078'
            );
        }
        $this->publicationLanguage = $publicationLanguage;
        $this->publicationDate = $publicationDate;
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getPublicationName(): string
    {
        return $this->publicationName;
    }

    /**
     * @param string $publicationName
     *
     * @return GoogleNewsUrlDecorator
     */
    public function setPublicationName(string $publicationName): GoogleNewsUrlDecorator
    {
        $this->publicationName = $publicationName;

        return $this;
    }

    /**
     * @return string
     */
    public function getPublicationLanguage(): string
    {
        return $this->publicationLanguage;
    }

    /**
     * @param string $publicationLanguage
     *
     * @return GoogleNewsUrlDecorator
     */
    public function setPublicationLanguage(string $publicationLanguage): GoogleNewsUrlDecorator
    {
        $this->publicationLanguage = $publicationLanguage;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getAccess()
    {
        return $this->access;
    }

    /**
     * @param string $access
     *
     * @return GoogleNewsUrlDecorator
     * @throws Exception\GoogleNewsUrlException
     */
    public function setAccess(string $access): GoogleNewsUrlDecorator
    {
        if ($access && !in_array($access, [self::ACCESS_REGISTRATION, self::ACCESS_SUBSCRIPTION])) {
            throw new Exception\GoogleNewsUrlException(
                sprintf(
                    'The parameter %s must be a valid access. See https://support.google.com/webmasters/answer/74288?hl=en&ref_topic=10078',
                    $access
                )
            );
        }
        $this->access = $access;

        return $this;
    }

    /**
     * @return array
     */
    public function getGenres(): array
    {
        return $this->genres;
    }

    /**
     * @param array $genres
     *
     * @return GoogleNewsUrlDecorator
     */
    public function setGenres(array $genres): GoogleNewsUrlDecorator
    {
        $this->genres = $genres;

        return $this;
    }

    /**
     * @param string $genre
     *
     * @return GoogleNewsUrlDecorator
     */
    public function addGenre(string $genre): GoogleNewsUrlDecorator
    {
        $this->genres[] = $genre;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getPublicationDate(): \DateTime
    {
        return $this->publicationDate;
    }

    /**
     * @param \DateTime $publicationDate
     *
     * @return GoogleNewsUrlDecorator
     */
    public function setPublicationDate(\DateTime $publicationDate): GoogleNewsUrlDecorator
    {
        $this->publicationDate = $publicationDate;

        return $this;
    }

    /**
     * @return string
     */
    public function getPublicationDateFormat(): string
    {
        return $this->publicationDateFormat;
    }

    /**
     * @param string $publicationDateFormat
     *
     * @return GoogleNewsUrlDecorator
     * @throws Exception\GoogleNewsUrlException
     */
    public function setPublicationDateFormat(string $publicationDateFormat): GoogleNewsUrlDecorator
    {
        $formats = [self::DATE_FORMAT_DATE, self::DATE_FORMAT_DATE_TIME];
        if ($publicationDateFormat && !in_array($publicationDateFormat, $formats)) {
            throw new Exception\GoogleNewsUrlException(
                sprintf(
                    'The parameter %s must be a valid date format. See https://support.google.com/webmasters/answer/74288?hl=en',
                    $publicationDateFormat
                )
            );
        }
        $this->publicationDateFormat = $publicationDateFormat;

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
     * @param string $title
     *
     * @return GoogleNewsUrlDecorator
     */
    public function setTitle(string $title): GoogleNewsUrlDecorator
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getGeoLocations()
    {
        return $this->geoLocations;
    }

    /**
     * @param string $geoLocations
     *
     * @return GoogleNewsUrlDecorator
     * @throws Exception\GoogleNewsUrlException
     */
    public function setGeoLocations(string $geoLocations): GoogleNewsUrlDecorator
    {
        $locationParts = explode(', ', $geoLocations);
        if (count($locationParts) < 2) {
            throw new Exception\GoogleNewsUrlException(
                sprintf(
                    'The parameter %s must be a valid geo_location. See https://support.google.com/news/publisher/answer/1662970?hl=en',
                    $geoLocations
                )
            );
        }
        $this->geoLocations = $geoLocations;

        return $this;
    }

    /**
     * @return array
     */
    public function getKeywords(): array
    {
        return $this->keywords;
    }

    /**
     * @param array $keywords
     *
     * @return GoogleNewsUrlDecorator
     */
    public function setKeywords(array $keywords): GoogleNewsUrlDecorator
    {
        $this->keywords = $keywords;

        return $this;
    }

    /**
     * @param string $keyword
     *
     * @return GoogleNewsUrlDecorator
     */
    public function addKeyword(string $keyword): GoogleNewsUrlDecorator
    {
        $this->keywords[] = $keyword;

        return $this;
    }

    /**
     * @return array
     */
    public function getStockTickers(): array
    {
        return $this->stockTickers;
    }

    /**
     * @param array $stockTickers
     *
     * @return GoogleNewsUrlDecorator
     * @throws Exception\GoogleNewsUrlException If the stock ticker limit is reached
     */
    public function setStockTickers(array $stockTickers): GoogleNewsUrlDecorator
    {
        if ($stockTickers && count($stockTickers) > 5) {
            throw new Exception\GoogleNewsUrlException(
                'The stock tickers are limited to 5. See https://support.google.com/webmasters/answer/74288?hl=en&ref_topic=10078'
            );
        }
        $this->stockTickers = $stockTickers;

        return $this;
    }

    /**
     * @param string $stockTicker
     *
     * @return GoogleNewsUrlDecorator
     * @throws Exception\GoogleNewsUrlException If the stock ticker limit is reached
     */
    public function addStockTicker(string $stockTicker): GoogleNewsUrlDecorator
    {
        if ($this->stockTickers && count($this->stockTickers) == 5) {
            throw new Exception\GoogleNewsUrlException(
                'The stock tickers are limited to 5. See https://support.google.com/webmasters/answer/74288?hl=en&ref_topic=10078'
            );
        }
        $this->stockTickers[] = $stockTicker;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function toXml(): string
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

        $newsXml .= '<news:publication_date>' . $this->getPublicationDate()->format(
                $this->getPublicationDateFormat()
            ) . '</news:publication_date>';

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
