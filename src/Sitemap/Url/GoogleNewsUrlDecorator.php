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

use DateTime;
use DateTimeInterface;
use Presta\SitemapBundle\Exception;
use Presta\SitemapBundle\Sitemap\Utils;

/**
 * Url decorator to add news information to a sitemap Url.
 *
 * https://developers.google.com/search/docs/advanced/sitemaps/news-sitemap
 */
class GoogleNewsUrlDecorator extends UrlDecorator
{
    public const ACCESS_SUBSCRIPTION = 'Subscription';
    public const ACCESS_REGISTRATION = 'Registration';

    public const DATE_FORMAT_DATE = 'Y-m-d';
    public const DATE_FORMAT_DATE_TIME = DateTime::W3C;

    /**
     * @var array<string, string>
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
     * @var array<int, string>
     */
    private $genres = [];

    /**
     * @var DateTimeInterface
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
     * @var array<int, string>
     */
    private $keywords = [];

    /**
     * @var array<int, string>
     */
    private $stockTickers = [];

    /**
     * @param Url               $urlDecorated
     * @param string            $publicationName
     * @param string            $publicationLanguage
     * @param DateTimeInterface $publicationDate
     * @param string            $title
     *
     * @throws Exception\GoogleNewsUrlException
     */
    public function __construct(
        Url $urlDecorated,
        string $publicationName,
        string $publicationLanguage,
        DateTimeInterface $publicationDate,
        string $title
    ) {
        parent::__construct($urlDecorated);

        $this->setPublicationName($publicationName);
        $this->setPublicationLanguage($publicationLanguage);
        $this->setPublicationDate($publicationDate);
        $this->setTitle($title);
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
    public function setPublicationName(string $publicationName): self
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
    public function setPublicationLanguage(string $publicationLanguage): self
    {
        if (strlen($publicationLanguage) > 5) {
            throw new Exception\GoogleNewsUrlException(
                'Use a 2 oder 3 character long ISO 639 language code. Except for chinese use zh-cn or zh-tw.' .
                'See https://support.google.com/webmasters/answer/74288?hl=en&ref_topic=10078'
            );
        }
        $this->publicationLanguage = $publicationLanguage;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getAccess(): ?string
    {
        return $this->access;
    }

    /**
     * @param string|null $access
     *
     * @return GoogleNewsUrlDecorator
     * @throws Exception\GoogleNewsUrlException
     */
    public function setAccess(?string $access): self
    {
        if ($access && !in_array($access, [self::ACCESS_REGISTRATION, self::ACCESS_SUBSCRIPTION])) {
            throw new Exception\GoogleNewsUrlException(
                sprintf(
                    'The parameter %s must be a valid access.' .
                    ' See https://support.google.com/webmasters/answer/74288?hl=en&ref_topic=10078',
                    $access
                )
            );
        }
        $this->access = $access;

        return $this;
    }

    /**
     * @return array<int, string>
     */
    public function getGenres(): array
    {
        return $this->genres;
    }

    /**
     * @param array<int, string> $genres
     *
     * @return GoogleNewsUrlDecorator
     */
    public function setGenres(array $genres): self
    {
        $this->genres = [];
        foreach ($genres as $genre) {
            $this->addGenre($genre);
        }

        return $this;
    }

    /**
     * @param string $genre
     *
     * @return GoogleNewsUrlDecorator
     */
    public function addGenre(string $genre): self
    {
        $this->genres[] = $genre;

        return $this;
    }

    /**
     * @return DateTimeInterface
     */
    public function getPublicationDate(): DateTimeInterface
    {
        return $this->publicationDate;
    }

    /**
     * @param DateTimeInterface $publicationDate
     *
     * @return GoogleNewsUrlDecorator
     */
    public function setPublicationDate(DateTimeInterface $publicationDate): self
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
    public function setPublicationDateFormat(string $publicationDateFormat): self
    {
        $formats = [self::DATE_FORMAT_DATE, self::DATE_FORMAT_DATE_TIME];
        if ($publicationDateFormat && !in_array($publicationDateFormat, $formats)) {
            throw new Exception\GoogleNewsUrlException(
                sprintf(
                    'The parameter %s must be a valid date format.' .
                    ' See https://support.google.com/webmasters/answer/74288?hl=en',
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
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getGeoLocations(): ?string
    {
        return $this->geoLocations;
    }

    /**
     * @param string $geoLocations
     *
     * @return GoogleNewsUrlDecorator
     * @throws Exception\GoogleNewsUrlException
     */
    public function setGeoLocations(string $geoLocations): self
    {
        $locationParts = explode(', ', $geoLocations);
        if (count($locationParts) < 2) {
            throw new Exception\GoogleNewsUrlException(
                sprintf(
                    'The parameter %s must be a valid geo_location.' .
                    ' See https://support.google.com/news/publisher/answer/1662970?hl=en',
                    $geoLocations
                )
            );
        }
        $this->geoLocations = $geoLocations;

        return $this;
    }

    /**
     * @return array<int, string>
     */
    public function getKeywords(): array
    {
        return $this->keywords;
    }

    /**
     * @param array<int, string> $keywords
     *
     * @return GoogleNewsUrlDecorator
     */
    public function setKeywords(array $keywords): self
    {
        $this->keywords = [];
        foreach ($keywords as $keyword) {
            $this->addKeyword($keyword);
        }

        return $this;
    }

    /**
     * @param string $keyword
     *
     * @return GoogleNewsUrlDecorator
     */
    public function addKeyword(string $keyword): self
    {
        $this->keywords[] = $keyword;

        return $this;
    }

    /**
     * @return array<int, string>
     */
    public function getStockTickers(): array
    {
        return $this->stockTickers;
    }

    /**
     * @param array<int, string> $stockTickers
     *
     * @return GoogleNewsUrlDecorator
     * @throws Exception\GoogleNewsUrlException If the stock ticker limit is reached
     */
    public function setStockTickers(array $stockTickers): self
    {
        if ($stockTickers && count($stockTickers) > 5) {
            throw new Exception\GoogleNewsUrlException(
                'The stock tickers are limited to 5.' .
                ' See https://support.google.com/webmasters/answer/74288?hl=en&ref_topic=10078'
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
    public function addStockTicker(string $stockTicker): self
    {
        if ($this->stockTickers && count($this->stockTickers) == 5) {
            throw new Exception\GoogleNewsUrlException(
                'The stock tickers are limited to 5.' .
                ' See https://support.google.com/webmasters/answer/74288?hl=en&ref_topic=10078'
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
        $newsXml .= '<news:name>' . Utils::cdata($this->getPublicationName()) . '</news:name>';
        $newsXml .= '<news:language>' . $this->getPublicationLanguage() . '</news:language>';
        $newsXml .= '</news:publication>';

        if ($this->getAccess()) {
            $newsXml .= '<news:access>' . $this->getAccess() . '</news:access>';
        }

        if (count($this->getGenres()) > 0) {
            $newsXml .= '<news:genres>' . implode(', ', $this->getGenres()) . '</news:genres>';
        }

        $newsXml .= '<news:publication_date>';
        $newsXml .= $this->getPublicationDate()->format($this->getPublicationDateFormat());
        $newsXml .= '</news:publication_date>';

        $newsXml .= '<news:title>' . Utils::cdata($this->getTitle()) . '</news:title>';

        if ($this->getGeoLocations()) {
            $newsXml .= '<news:geo_locations>' . $this->getGeoLocations() . '</news:geo_locations>';
        }

        if (count($this->getKeywords()) > 0) {
            $newsXml .= '<news:keywords>' . implode(', ', $this->getKeywords()) . '</news:keywords>';
        }

        if (count($this->getStockTickers()) > 0) {
            $newsXml .= '<news:stock_tickers>' . implode(', ', $this->getStockTickers()) . '</news:stock_tickers>';
        }

        $newsXml .= '</news:news>';

        $baseXml = $this->urlDecorated->toXml();

        return str_replace('</url>', $newsXml . '</url>', $baseXml);
    }
}
