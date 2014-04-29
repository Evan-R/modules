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

use \DOMXpath;
use \DOMDocument as BaseDOM;

/**
 * @class DOMDocument extends BaseDom
 * @see BaseDom
 *
 * @package Selene\Components\Xml
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class DOMDocument extends BaseDom
{
    /**
     * xpath
     *
     * @var DOMXpath
     */
    protected $xpath;

    /**
     * __construct
     *
     * @param mixed $version
     * @param mixed $encoding
     * @access public
     * @return mixed
     */
    public function __construct($version = null, $encoding = null)
    {
        parent::__construct($version, $encoding);
        $this->registerNodeClass('DOMElement', __NAMESPACE__ . '\\DOMElement');
    }

    /**
     * __clone
     *
     * @access public
     * @return void
     */
    public function __clone()
    {
        if ($this->xpath) {
            $this->xpath = new DOMXpath($this);
        }
    }

    /**
     * xPath
     *
     * @param mixed $query
     * @access public
     * @return DOMNodeList
     */
    public function xpath($query, DOMNode $contextNode = null)
    {
        return $this->getXpath()->query($query, $contextNode);
    }

    /**
     * importElement
     *
     * @param DOMElement $import
     * @param DOMElement $element
     * @access public
     * @return mixed
     */
    public function appendDomElement(\DOMElement $import, \DOMElement $element = null, $deep = true)
    {
        $import = $this->importNode($import, $deep);

        if (is_null($element)) {
            return $this->firstChild->appendChild($import);
        }

        return $element->appendChild($import);
    }

    /**
     * getXpath
     *
     * @access protected
     * @return DOMXpath
     */
    public function getXpath()
    {
        if (!$this->xpath) {
            $this->xpath = new DOMXpath($this);
        }
        return $this->xpath;
    }
}
