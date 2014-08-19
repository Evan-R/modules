<?php

/*
 * This File is part of the Selene\Module\Config package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Config\Resource;

/**
 * @interface LocatorInterface
 *
 * @package Selene\Module\Config
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
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
     * @return string|array
     */
    public function locate($file, $collect = false);

    /**
     * Add a resource path
     *
     * @param string $path a resource path.
     *
     * @return void
     */
    public function addPath($path);

    /**
     * Add resource paths
     *
     * @param array $paths a collection of resource paths.
     *
     * @return void
     */
    public function addPaths(array $paths);

    /**
     * Set resource paths
     *
     * @param array $paths a collection of resource paths.
     *
     * @return void
     */
    public function setPaths(array $paths);

    /**
     * Set the location root path.
     *
     * @param string $root
     *
     * @return void
     */
    public function setRootPath($root);
}
