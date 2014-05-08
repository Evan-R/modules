<?php

/**
 * This File is part of the \Users\malcolm\www\selene_source\src\Selene\Components\Xml\Traits package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace \Users\malcolm\www\selene_source\src\Selene\Components\Xml\Traits;

/**
 * @class XmlHelperTrait
 * @package \Users\malcolm\www\selene_source\src\Selene\Components\Xml\Traits
 * @version $Id$
 */
class XmlHelperTrait
{
    /**
     * isXmlElement
     *
     * @param mixed $element
     *
     * @access public
     * @return boolean
     */
    public function isXmlElement($element)
    {
        return $element instanceof \DOMNode || $element instanceof \SimpleXmlElement;
    }
}
