<?php

/**
 * This File is part of the Selene\Components\Xml package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Xml\Dom;

use \DOMElement as BaseDOMElement;

/**
 * @class DOMElement extends BaseDOMElement
 * @see BaseDOMElement
 *
 * @package Selene\Components\Xml\Dom
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class DOMElement extends BaseDOMElement
{
    /**
     * xPath
     *
     * @access public
     * @return mixed
     */
    public function xPath($query)
    {
        return $this->ownerDocument->getXpath()->query($query, $this);
    }

    /**
     * appendDomElement
     *
     * @param DOMElement $import
     * @access public
     * @return mixed
     */
    public function appendDomElement(\DOMElement $import, $deep = true)
    {
        return $this->ownerDocument->appendDomElement($import, $this, $deep);
    }
}
