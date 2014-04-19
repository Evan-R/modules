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

use \Selene\Components\Filesystem\Filesystem;

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
     * fs
     *
     * @var \Selene\Components\Filesystem\Filesystem
     */
    protected $fs;

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
    public function __construct($file, Filesystem $files = null)
    {
        $this->resource = $file;
        $this->fs = $files;
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
    abstract public function isValid($timestamp);

    /**
     * getPath
     *
     * @access public
     * @return string
     */
    public function getPath()
    {
        return $this->resource;
    }
}
