<?php

/**
 * This File is part of the Selene\Components\Filesystem package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Filesystem;

use Selene\Components\Common\Interfaces\JsonableInterface;
use Selene\Components\Common\Interfaces\ArrayableInterface;

/**
 * @class AbstractFileObject
 * @see ArrayableInterface
 * @see JsonableInterface
 * @abstract
 *
 * @package Selene\Components\Filesystem
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com
 * @license MIT
 */
abstract class AbstractFileObject implements ArrayableInterface, JsonableInterface
{
    /**
     * files
     *
     * @var Filesystem
     */
    protected $files;

    /**
     * filepath
     *
     * @var string
     */
    protected $path;

    public function __construct(Filesystem $files, $path)
    {
        $this->files     = $files;
        $this->setPath($path);
    }

    /**
     * @access public
     * @return string
     */
    public function __toString()
    {
        return $this->path;
    }

    /**
     * remove
     *
     *
     * @access public
     * @return Directory
     */
    public function remove()
    {
        $this->files->remove($this->path);
        return $this;
    }

    /**
     * copy
     *
     * @param mixed $target
     *
     * @access public
     * @return mixed
     */
    public function copy($target = null)
    {
        $this->files->copy($this->path, $target);
        return $this;
    }

    /**
     * rename
     *
     * @param mixed $newName
     *
     * @access public
     * @return File
     */
    public function rename($newName)
    {
        $this->files->rename($path = $this->path, dirname($path).DIRECTORY_SEPARATOR.trim($newName, '\\\/'));
        return $this;
    }

    /**
     * move
     *
     * @param string $location
     *
     * @access public
     * @return File
     */
    public function move($location)
    {
        $this->files->rename($this->path, $location);
        return $this;
    }

    /**
     * toJson
     *
     * @access public
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->toArray(), defined('JSON_PRETTY_PRINT') ? JSON_PRETTY_PRINT : 0);
    }

    /**
     * getInfo
     *
     * @access public
     * @return SplFileInfo
     */
    public function getInfo()
    {
        return new SplFileInfo((string)$this);
    }

    /**
     * getRealPath
     *
     * @param mixed $file
     *
     * @access public
     * @return string
     */
    public function getRealPath($file = null){
        return is_null($file) ? (string)$this : ($this->files->isAbsolutePath($file) ? $file : (string)($this) . $file);
    }

    /**
     * Set the $path property.
     *
     * @param string $path
     *
     * @access protected
     * @abstract
     * @return void
     */
    abstract protected function setPath($path);
}
