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
     * depthsFilter
     *
     * @var int
     */
    protected $depthsFilter;

    /**
     * notInFilter
     *
     * @var mixed
     */
    protected $notInFilter;

    /**
     * onlyFilesFilter
     *
     * @var mixed
     */
    protected $onlyFilesFilter = false;

    /**
     * ignoreFilter
     *
     * @var mixed
     */
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
     * @return Directory
     */
    public function mkdir($dir, $permissions = 0755, $recursive = true)
    {
        $this->files->mkdir((string)$this.DIRECTORY_SEPARATOR.rtrim($dir, '\\\/'), $permissions, $recursive);
        return $this;
    }

    public function flush()
    {
        $this->files->flush((string)$this);
        return $this;
    }

    /**
     * isEmpty
     *
     * @param mixed $directory
     *
     * @access public
     * @return bool
     */
    public function isEmpty($directory = null)
    {
        return $this->files->isEmpty($this->getRealPath($directory));
    }

    /**
     * touch
     *
     * @param mixed $file
     * @param mixed $time
     * @param mixed $atime
     *
     * @access public
     * @return Directory
     */
    public function touch($file, $time = null, $atime = null)
    {
        $this->files->touch($this->getRealPath($file), $time, $atime);
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
     * exists
     *
     * @param mixed $file
     *
     * @access public
     * @return boolean
     */
    public function exists($file)
    {
        return $this->files->exists($this->getRealPath($file));
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
        return $this->listDir(
            new FileCollection($path = (string)$this),
            $path,
            0 === $this->depthsFilter ? false : true
        );
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

        $count = true;


        foreach ($iterator = $this->getIterator($location) as $fileInfo) {

            if ($fileInfo->isLink()) {
                continue;
            }

            if (true !== $ignorefiles and $fileInfo->isFile() and $this->isIncludedFile($fileInfo->getBaseName())) {
                $collection->add($fileInfo->getFileInfo());
                continue;
            }

            if ($recursive and $fileInfo->isDir()) {


                if ($this->isIncludedDir($fileInfo->getRealPath()) and true !== $this->onlyFilesFilter) {
                    $collection->add($fileInfo->getFileInfo());
                }

                if (isset($this->depthsFilter)) {
                    if (0 === $this->depthsFilter) {
                        continue;
                    }
                    if (false !== $count) {
                        $this->depthsFilter--;
                        $count = false;
                    }
                }

                $this->doList($collection, $fileInfo->getRealPath(), true, $ignorefiles);
                continue;
            }
        }
    }

    private function countDepth($path)
    {

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
        unset($this->depthsFilter);
        unset($this->ignoreFilter);
        unset($this->currentFilter);
        unset($this->currentExclude);

        $this->inFilter       = null;
        $this->fileFilter     = null;
        $this->notInFilter    = null;
        $this->depthsFilter   = null;
        $this->ignoreFilter   = null;
        $this->currentFilter  = null;
        $this->currentExclude = null;

        $this->onlyFilesFilter = false;
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
        // need to check agains vcs ignore pattern
        if ($this->isIgnoredFile($dir)) {
            return false;
        }
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
     * depth
     *
     * @param mixed $depths
     *
     * @access public
     * @return mixed
     */
    public function depth($depths)
    {
        $this->depthsFilter = (int)$depths;
        return $this;
    }

    public function files()
    {
        $this->onlyFilesFilter = true;
        return $this;
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
        //$this->issnottheend;

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
