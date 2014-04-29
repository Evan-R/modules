<?php

/**
 * This File is part of the Selene\Components\Xml package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Xml;

use \Selene\Components\Xml\Dom\DOMElement;

/**
 * @interface ParserInterface
 *
 * @package Selene\Components\Xml
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
interface ParserInterface
{
    public function parse($xml);

    public function parseDom(\DOMDocument $xml);

    public function parseDomElement(DOMElement $param);
}
