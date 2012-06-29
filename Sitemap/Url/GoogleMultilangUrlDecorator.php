<?php
namespace Presta\SitemapBundle\Sitemap\Url;

use Presta\SitemapBundle\Exception;

/**
 * Decorate w/ google alternate language url guidelines
 * @see http://support.google.com/webmasters/bin/answer.py?hl=en&answer=2620865
 * 
 * @author David Epely 
 */
class GoogleMultilangUrlDecorator extends UrlDecorator
{
    const REL_ALTERNATE = 'alternate';
    
    protected $customNamespaces = array('xhtml' => 'http://www.w3.org/1999/xhtml');
    protected $linkXml = '';
    
    /**
     * add an alternative language to the url
     * 
     * @param string $href - valid url of the translated page
     * @param string $hreflang - valid language code @see http://www.w3.org/TR/xhtml-modularization/abstraction.html#dt_LanguageCode
     * @param string $rel (default is alternate) - valid link type @see http://www.w3.org/TR/xhtml-modularization/abstraction.html#dt_LinkTypes
     */
    public function addLink($href, $hreflang, $rel = null)
    {
        $this->linkXml .= $this->generateLinkXml($href, $hreflang, $rel);
    }
    
    /**
     * @param string $href
     * @param string $hreflang
     * @param string $rel
     * @return string 
     */
    protected function generateLinkXml($href, $hreflang, $rel = null)
    {
        if (null == $rel) {
            $rel = self::REL_ALTERNATE;
        }
        
        $xml = '<xhtml:link rel="' . $rel 
                . '" hreflang="' . $hreflang 
                . '" href="' . $href . '" />';
        
        return $xml;
    }


    /**
     * add link elements before the closing tag
     * 
     * @return string 
     */
    public function toXml()
    {
        $baseXml = $this->urlDecorated->toXml();
        return str_replace('</url>', $this->linkXml . '</url>', $baseXml);
    }
}