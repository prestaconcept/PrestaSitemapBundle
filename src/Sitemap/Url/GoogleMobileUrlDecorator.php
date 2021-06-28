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

/**
 * Decorate url for mobile website
 *
 * @see http://support.google.com/webmasters/bin/answer.py?hl=en&hlrm=fr&answer=34648
 */
class GoogleMobileUrlDecorator extends UrlDecorator
{
    /**
     * @var array<string, string>
     */
    protected $customNamespaces = ['mobile' => 'http://www.google.com/schemas/sitemap-mobile/1.0'];

    /**
     * @inheritdoc
     */
    public function toXml(): string
    {
        return str_replace('</url>', '<mobile:mobile/></url>', $this->urlDecorated->toXml());
    }
}
