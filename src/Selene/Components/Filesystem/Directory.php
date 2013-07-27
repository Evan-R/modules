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

use FilesystemIterator;
use Selene\Components\Common\Interfaces\JsonableInterface;
use Selene\Components\Common\Interfaces\ArrayableInterface;

/**
 * @class Directory
 * @see ArrayableInterface
 * @see JsonableInterface
 *
 * @package Selene\Components\Filesystem
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com
 * @license MIT
 */
class Directory extends AbstractFileObject implements ArrayableInterface, JsonableInterface
{

    /**
     * currentExclude
     *
     * @var string|array
     */
    protected $currentExclude;

    /**
     * currentFilter
     *
     * @var string|array
     */
    protected $currentFilter;

    /**
     * fileFilter
     *
     * @var mixed
     */
    protected $fileFilter = [];

    /**
     * inFilter
     *
     * @var mixed
     */
    protected $inFilter = [];

    /**
     * notInFilter
     *
     * @var mixed
     */
    protected $notInFilter = [];

    /**
     * vcsPattern
     *
     * @var array
     */
    protected static $vcsPattern = [
        '.git.*', '.svn.*'
    ];

    /**
     * mkdir
     *
     * @param mixed $dir
     *
     * @access public
     * @return mixed
     */
    public function mkdir($dir, $permissions = 0755, $recursive = true)
    {
        $this->files->mkdir((string)$this.DIRECTORY_SEPARATOR.rtrim($dir, '\\\/'), $permissions, $recursive);
        return $this;
    }

    /**
     * remove
     *
     *
     * @access public
     * @return boolean
     */
    public function remove()
    {
        $this->files->remove((string)$this);
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
        $this->files->copy((string)$this, $target);
        return $this;
    }

    /**
     * chmod
     *
     * @param int  $permissions
     * @param bool $recursive
     *
     * @access public
     * @return boolean
     */
    public function chmod($permissions, $recursive = false)
    {
        $this->files->chmod((string)$this, $permissions, $recursive);
        return $this;
    }

    /**
     * chown
     *
     * @param int  $permissions
     * @param bool $recursive
     *
     * @access public
     * @return boolean
     */
    public function chown($owner, $recursive = false)
    {
        $this->files->chown((string)$this, $owner, $recursive);
        return $this;
    }

    /**
     * chgrp
     *
     * @param int  $permissions
     * @param bool $recursive
     *
     * @access public
     * @return boolean
     */
    public function chgrp($group, $recursive = false)
    {
        $this->files->chgrp((string)$this, $group, $recursive);
        return $this;
    }

    /**
     * files
     *
     *
     * @access public
     * @return mixed
     */
    public function files()
    {
        $this->listDir($collection = new FileCollection((string)$this), $this->currentFilter, false);
        return $collection;
    }

    /**
     * clealFilter
     *
     * @access public
     * @return void
     */
    public function clealFilter()
    {
        $this->currentFilter  = null;
        $this->currentExclude = null;
    }

    /**
     * get
     *
     *
     * @access public
     * @return mixed
     */
    public function get()
    {
        $this->listDir(
            $collection = new FileCollection((string)$this),
            $this->currentFilter ? $this->currentFilter : '.*',
            true,
            $this->currentExclude ? $this->currentExclude : '.*'
        );
        return $collection;
    }

    /**
     * toArrray
     *
     * @access public
     * @return array
     */
    public function toArray()
    {
        $this->listDir($collection = new FileCollection((string)$this), true);
        return $collection->toArray();
    }

    /**
     * listDir
     *
     * @param FileCollection $collection
     * @param mixed $directory
     * @param mixed $filter
     * @param mixed $exclude
     *
     * @access protected
     * @return void
     */
    protected function listDir(FileCollection $collection, $filter = null, $recursive = true, $exclude = [])
    {
        foreach ($this->getIterator((string)$this) as $fileInfo) {

            if ($fileInfo->isFile()) {
                if ($this->matchExpression($this->expandRegexp($filter), $fileInfo->getPathName())) {
                    $collection->add($fileInfo->getFileInfo());
                }
                continue;
            }

            if ($recursive and $fileInfo->isDir()) {
                if (!$this->matchExpression($this->expandRegexp($filter), $fileInfo->getPathName())) {
                    $this->listDir($collection, $fileInfo->getPathName(), $filter, $exclude);
                }
                continue;
            }
        }
    }

    /**
     * filter
     *
     * @access public
     * @return mixed
     */
    public function filter($expression)
    {
        return $this;
    }

    /**
     * in
     *
     * @access public
     * @return mixed
     */
    public function in($directories)
    {
        $this->getDirectoryExpression($directories);
        return $this;
    }

    /**
     * notIn
     *
     * @access public
     * @return Directory
     */
    public function notIn($directories)
    {
        $this->getDirectoryExpression($directories);
        return $this;
    }

    /**
     * getDirectoryExpression
     *
     * @param mixed $directories
     *
     * @access protected
     * @return mixed
     */
    protected function getDirectoryExpression($directories)
    {
        $directories = is_array($directories) ?
            ($path = '^'.(string)$this.DIRECTORY_SEPARATOR)
            .implode('|'.$path, str_replace('\\\//', DIRECTORY_SEPARATOR, $directories)) :
            (string)$directories;

        return $directories;
    }

    /**
     * flushFilter
     *
     * @access protected
     * @return mixed
     */
    protected function flushFilter()
    {

    }

    /**
     * setDirectory
     *
     * @param string $dir
     *
     * @access protected
     * @return void
     */
    protected function setPath($path)
    {
        if (!$this->files->isDir($path)) {
            throw new IOException(
                sprintf('%s is not a directory', $path)
            );
        }
        $this->path = realpath($path);
    }

    /**
     * getIterator
     *
     * @param mixed $directory
     *
     * @access protected
     * @return DirectoryIterator
     */
    protected function getIterator($directory, $flags = FilesystemIterator::SKIP_DOTS)
    {
        $iterator = new FilesystemIterator(
            $directory,
            FilesystemIterator::CURRENT_AS_SELF|$flags
        );
        $iterator->setInfoClass(__NAMESPACE__.'\\SplFileInfo');
        return $iterator;
    }

    /**
     * expandRegexp
     *
     * @access protected
     * @return string
     */
    protected function expandRegexp($expression)
    {
        return sprintf('#(%s)$#', $expression);
    }

    /**
     * getExludeList
     *
     * @access protected
     * @return array
     */
    protected function getExludeList()
    {

    }

    /**
     * matchExpression
     *
     *
     * @access protected
     * @return mixed
     */
    protected function matchExpression($expression, $value)
    {
        return (bool)preg_match($expression, $value);
    }
}
