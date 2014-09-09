<?php

/*
 * This File is part of the Selene\Module\Common\Traits package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Common\Traits;

use \Selene\Module\Common\Helper\ListHelper;

/**
 * @trait Setter
 *
 * @package Selene\Module\Common\Traits
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
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
        $resource[$attribute] = $value ?: $default;
    }

    protected function setDefaultArray(array &$resource, $attribute, $value = null, $default = null)
    {
        return ListHelper::arraySet($resource, $attribute, $value ?: $default);
    }

    protected function unsetInArray(array &$resource, $attribute, $separator)
    {
        return ListHelper::arrayUnset($resource, $attribute, $separator);
    }
}
