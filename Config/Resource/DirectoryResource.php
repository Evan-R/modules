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
 * @class DirectoryResource extends Resource
 * @see Resource
 *
 * @package Selene\Components\Config\Resource
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class DirectoryResource extends AbstractResource
{
    /**
     * isValid
     *
     * @param integer $timestamp
     *
     * @access public
     * @return mixed
     */
    public function exists()
    {
        return is_dir($this->resource);
    }
}
