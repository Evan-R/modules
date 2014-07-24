<?php

/**
 * This File is part of the Selene\Components\Config\Resource package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Config\Resource;

/**
 * @class PhpFileLoader
 * @package Selene\Components\Config\Resource
 * @version $Id$
 */
class PhpFileLoader extends Loader
{
    protected function doLoad($resource)
    {
        include $resource;
    }
}
