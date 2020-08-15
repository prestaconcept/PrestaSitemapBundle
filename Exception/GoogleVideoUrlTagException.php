<?php

/**
 * This file is part of the PrestaSitemapBundle package.
 *
 * (c) PrestaConcept <www.prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle\Exception;

@trigger_error(sprintf("%s is deprecated. Use %s instead", GoogleVideoUrlTagException::class, GoogleVideoTagException::class));

/**
 * Exception used when limit is reached on adding tag to video
 *
 * @author David Epely <depely@prestaconcept.net>
 *
 * @deprecated Use \Presta\SitemapBundle\Exception\GoogleVideoTagException instead.
 */
class GoogleVideoUrlTagException extends Exception
{
}
