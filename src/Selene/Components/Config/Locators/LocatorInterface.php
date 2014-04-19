<?php

/**
 * This File is part of the Selene\Components\Config\Locators package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Config\Locators;

/**
 * @class LocatorInterface
 * @package Selene\Components\Config\Locators
 * @version $Id$
 */
interface LocatorInterface
{
    /**
     * locate
     *
     * @param mixed $file
     * @param mixed $collect
     *
     * @access public
     * @return string|array
     */
    public function locate($file, $collect = true);
}
