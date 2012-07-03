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
 * Decorate url for mobile website
 * 
 * @see http://support.google.com/webmasters/bin/answer.py?hl=en&hlrm=fr&answer=34648
 * 
 * @author David Epely 
 */
class GoogleMobileUrlDecorator extends UrlDecorator
{
    protected $customNamespaces = array('mobile' => 'http://www.google.com/schemas/sitemap-mobile/1.0');

    /**
     * add mobile element before the closing tag
     * 
     * @return string 
     */
    public function toXml()
    {
        $baseXml = $this->urlDecorated->toXml();
        return str_replace('</url>', '<mobile:mobile/></url>', $baseXml);
    }
}