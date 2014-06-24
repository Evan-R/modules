<?php

/**
 * This File is part of the Selene\Components\Config package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Config\Resource;

/**
 * @class FileResource extends AbstractResource
 * @see AbstractResource
 *
 * @package Selene\Components\Config
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class FileResource extends AbstractResource
{
    /**
     * exists
     *
     *
     * @access public
     * @return boolean
     */
    public function exists()
    {
        return is_file($this->getPath());
    }
}
