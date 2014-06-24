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
 * @interface LocatorInterface
 *
 * @package Selene\Components\Config
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
interface LocatorInterface
{
    /**
     * Locate a resource file
     *
     * @param string  $file typically the filename
     * @param boolean $collect weather to collection all files within the given
     * paths or return the first match.
     *
     * @access public
     * @return string|array
     */
    public function locate($file, $collect = false);

    /**
     * Add a resource path
     *
     * @param string $path a resource path.
     *
     * @access public
     * @return void
     */
    public function addPath($path);

    /**
     * Add resource paths
     *
     * @param array $paths a collection of resource paths.
     *
     * @access public
     * @return void
     */
    public function addPaths(array $paths);
}
