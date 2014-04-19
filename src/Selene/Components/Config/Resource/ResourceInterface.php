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
 * @interface ResourceInterface ResourceInterface
 *
 * @package Selene\Components\Config
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
interface ResourceInterface
{
    /**
     * isValid
     *
     * @return boolean
     */
    public function isValid($timestamp);

    /**
     * Should return the resources file path.
     * @return string
     */
    public function getPath();
}
