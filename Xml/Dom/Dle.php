<?php

/**
 * This File is part of the Selene\Module\Xml\Tests\Dom package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Xml\Dom;

/**
 * @class Dle
 * @package Selene\Module\Xml\Tests\Dom
 * @version $Id$
 */
class Dle extends \DOMElement
{
    /**
     * xPath
     *
     * @access public
     * @return mixed
     */
    public function xpath($query)
    {
        if ($this->ownerDocument) {
            return $this->ownerDocument->getXpath()->query($query, $this);
        }

        throw new \BadMethodCallException('cannot xpath on element without an owner document');
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
        if ($this->ownerDocument) {
            return $this->ownerDocument->appendDomElement($import, $this, $deep);
        }

        throw new \BadMethodCallException('cannot add an element without an owner document');
    }
}
