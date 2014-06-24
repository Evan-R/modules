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
 * @class AbstractResource implements ResourceInterface
 * @see ResourceInterface
 *
 * @package Selene\Components\Config\Resource
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
abstract class AbstractResource implements ResourceInterface
{
    /**
     * resource
     *
     * @var string
     */
    protected $resource;

    /**
     * @param mixed $file
     * @param \Selene\Components\Filesystem\Filesystem $files
     *
     * @access public
     */
    public function __construct($resource)
    {
        $this->resource = $resource;
    }

    /**
     * isValid
     *
     * @param mixed $timestamp
     *
     * @access public
     * @abstract
     * @return boolean
     */
    public function isValid($timestamp)
    {
        return $this->exists() && filemtime($this->getPath()) < $timestamp;
    }

    /**
     * getResource
     *
     * @access public
     * @return mixed
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * getPath
     *
     * @access public
     * @return mixed
     */
    public function getPath()
    {
        return $this->getResource();
    }

    /**
     * __toString
     *
     *
     * @access public
     * @return string
     */
    public function __toString()
    {
        return $this->getPath();
    }
}
