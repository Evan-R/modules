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
 * @trait Getter
 *
 * @package Selene\Components\Common\Traits
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
trait Getter
{
    /**
     * getDefault
     *
     * @access protected
     * @return mixed
     */
    protected function getDefault(array &$resource, $attribute, $default = null)
    {
        if (isset($resource[$attribute])) {
            return $resource[$attribute];
        }
        return $default;
    }
}
