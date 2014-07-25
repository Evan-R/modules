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
 * @interface ResourceInterface
 *
 * @package Selene\Components\Config\Resource
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
interface ResourceInterface
{
    /**
     * Checks the validity of a resource agains a timestamp.
     *
     * @param int $timestamp a unix timestamp
     *
     * @return boolean
     */
    public function isValid($timestamp);

    /**
     * Checks if the resource exists.
     *
     * @return boolean
     */
    public function exists();
}
