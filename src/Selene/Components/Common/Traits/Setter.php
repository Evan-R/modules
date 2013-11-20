<?php

/**
 * This File is part of the Selene\Components\Common\Traits package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Common\Traits;

/**
 * @class Getter
 * @package Selene\Components\Common\Traits
 * @version $Id$
 */
trait Setter
{
    /**
     * setDefault
     *
     * @access protected
     * @return mixed
     */
    protected function setDefault(array &$resource, $attribute, $value = null, $default = null)
    {
        $resource[$attribute] = !$value ? $default : $value;
    }
}
