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

/**
 * Decorate url for mobile website
 *
 * @see http://support.google.com/webmasters/bin/answer.py?hl=en&hlrm=fr&answer=34648
 *
 * @author David Epely <depely@prestaconcept.net>
 */
class GoogleMobileUrlDecorator extends UrlDecorator
{
    /**
     * @var array
     */
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
