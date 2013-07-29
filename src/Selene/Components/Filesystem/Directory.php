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
use Selene\Components\Filesystem\Filter\FileFilter;
use Selene\Components\Filesystem\Filter\DirectoryFilter;
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
    protected $fileFilter;

    /**
     * inFilter
     *
     * @var mixed
     */
    protected $inFilter;

    /**
     * notInFilter
     *
     * @var mixed
     */
    protected $notInFilter;

    protected $ignoreFilter;

    /**
     * vcsPattern
     *
     * @var array
     */
    protected static $vcsPattern = [
        '\.git.*', '\.svn.*'
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
     * get
     *
     *
     * @access public
     * @return mixed
     */
    public function get()
    {
        return $this->listDir(new FileCollection($path = (string)$this), $path, true);
    }

    /**
     * toArrray
     *
     * @access public
     * @return array
     */
    public function toArray()
    {
        $this->clearFilter();
        return $this->get()->toArray();
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
     * @return FileCollection
     */
    protected function listDir(FileCollection $collection, $location, $recursive = true)
    {
        $this->setVcsFilter();
        $this->doList($collection, $location, $recursive);
        $this->clearFilter();

        return $collection;
    }

    /**
     * doList
     *
     * @param FileCollection $collection
     * @param mixed $location
     * @param mixed $recursive
     *
     * @access private
     * @return void
     */
    private function doList(FileCollection $collection, $location, $recursive = true, $ignorefiles = false)
    {

        if (!$this->isIncludedDir($location)) {
            $ignorefiles = true;
        }

        foreach ($iterator = $this->getIterator($location) as $fileInfo) {

            if ($fileInfo->isLink()) {
                continue;
            }

            if (!$ignorefiles and $fileInfo->isFile() and $this->isIncludedFile($fileInfo->getBaseName())) {
                $collection->add($fileInfo->getFileInfo());
                continue;
            }

            if ($recursive and $fileInfo->isDir()) {
                $this->doList($collection, $fileInfo->getRealPath(), true);
                if ($this->isIncludedDir($fileInfo->getRealPath())) {
                    $collection->add($fileInfo->getFileInfo());
                }
                continue;
            }
        }
    }

    /**
     * clealFilter
     *
     * @access private
     * @return void
     */
    private function clearFilter()
    {
        unset($this->inFilter);
        unset($this->fileFilter);
        unset($this->notInFilter);
        unset($this->ignoreFilter);
        unset($this->currentFilter);
        unset($this->currentExclude);

        $this->inFilter       = null;
        $this->fileFilter     = null;
        $this->notInFilter    = null;
        $this->ignoreFilter   = null;
        $this->currentFilter  = null;
        $this->currentExclude = null;
    }

    /**
     * setVcsFilter
     *
     *
     * @access protected
     * @return void
     */
    protected function setVcsFilter()
    {
        if (!isset($this->ignoreFilter)) {
            $this->ignoreFilter = new FileFilter(static::$vcsPattern);
        }
    }

    /**
     * isIncludedFile
     *
     * @param mixed $file
     *
     * @access protected
     * @return bool
     */
    protected function isIncludedFile($file)
    {
        if ($this->isIgnoredFile($file)) {
            return false;
        }
        return isset($this->fileFilter) ? $this->fileFilter->match($file) : true;
    }

    /**
     * isIgnoredFile
     *
     * @param mixed $file
     *
     * @access protected
     * @return bool
     */
    protected function isIgnoredFile($file)
    {
        return $this->ignoreFilter->match($file);
    }

    /**
     * isIncludedDir
     *
     * @param mixed $dir
     *
     * @access protected
     * @return bool
     */
    protected function isIncludedDir($dir)
    {
        return $this->isExcludedDir($dir) ?
            false :
            (isset($this->inFilter) ? $this->inFilter->match($dir) : true);
    }

    /**
     * isExcludedDir
     *
     * @param mixed $path
     *
     * @access protected
     * @return bool
     */
    protected function isExcludedDir($path)
    {
        return $this->isIgnoredFile(basename($path)) ? true : (
            (!isset($this->notInFilter) ? false : $this->notInFilter->match($path))
        );
    }

    /**
     * filter
     *
     * @access public
     * @return mixed
     */
    public function filter($expression)
    {
        $this->fileFilter = new FileFilter((array)$expression);
        return $this;
    }

    /**
     * ignore
     *
     * @param mixed $expression
     *
     * @access public
     * @return mixed
     */
    public function ignore($expression)
    {
        $pattern = static::$vcsPattern;
        if (is_array($expression)) {
            $pattern = array_merge($pattern, $expression);
        } else {
            array_push($pattern, $expression);
        }
        $this->ignoreFilter = new FileFilter($pattern);
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
        $this->inFilter = new DirectoryFilter((array)$directories, (string)$this);
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
        $this->notInFilter = new DirectoryFilter((array)$directories, (string)$this);
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
     * addVcsPattern
     *
     * @param mixed $pattern
     *
     * @access public
     * @return void
     */
    public static function addVcsPattern($pattern)
    {
        static::$vcsPattern = array_merge(static::$vcsPattern, (array)$pattern);
    }
}