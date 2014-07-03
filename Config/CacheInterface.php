<?php

/**
 * This File is part of the Selene\Components\Config package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Config;

/**
 * @interface CacheInterface CacheInterface
 *
 * @package Selene\Components\Config
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
interface CacheInterface
{
    public function setFile($file);

    public function getFile();

    /**
     * isValid
     *
     * @access public
     * @return boolean
     */
    public function isValid();

    /**
     * setDebug
     *
     * @param mixed $debug
     *
     * @access public
     * @return void
     */
    public function setDebug($debug);

    /**
     * write
     *
     * @param mixed $data
     * @param mixed $manifest
     *
     * @access public
     * @return void
     */
    public function write($data, array $manifest = null);

    /**
     * Invalidate the cache
     *
     * @access public
     * @return void
     */
    public function forget();
}
