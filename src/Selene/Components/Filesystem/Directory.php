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

use Selene\Components\Filesystem\FilesystemIterator;
use Selene\Components\Filesystem\Filter\FileFilter;
use Selene\Components\Filesystem\Filter\DirectoryFilter;
use Selene\Components\Common\Interfaces\JsonableInterface;
use Selene\Components\Common\Interfaces\ArrayableInterface;
use Selene\Components\Filesystem\Traits\SubstitudePath;

/**
 * @class Directory
 * @see ArrayableInterface
 * @see JsonableInterface
 *
 * @package Selene\Components\Filesystem
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class Directory extends AbstractFileObject
{

    use SubstitudePath;

    /**
     * @var int
     */
    const IGNORE_VCS = 1;

    /**
     * @var int
     */
    const IGNORE_DOT = 2;

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
     * startAtFilter
     *
     * @var string
     */
    protected $startAtFilter;

    /**
     * notInFilter
     *
     * @var mixed
     */
    protected $notInFilter;

    /**
     * includeSelfFilter
     *
     * @var bool
     */
    protected $includeSelfFilter;

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
     * ignoreVcs
     *
     * @var mixed
     */
    protected $ignoreVcs;

    /**
     * ignoreDot
     *
     * @var mixed
     */
    protected $ignoreDot;

    /**
     * vcsPattern
     *
     * @var array
     */
    protected static $vcsPattern = [
        '\.git.*', '\.svn.*'
    ];

    /**
     * __construct
     *
     * @param Filesystem $files
     * @param mixed $path
     * @param mixed $flags
     *
     * @access public
     * @return mixed
     */
    public function __construct(Filesystem $files, $path, $flags = self::IGNORE_VCS)
    {
        parent::__construct($files, $path);

        $this->ignoreVcs = (bool)($flags & static::IGNORE_VCS);
        $this->ignoreDot = (bool)($flags & static::IGNORE_DOT);
    }

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

    /**
     * alter timestamps on a file.
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
     * mask
     *
     * @param mixed $mode
     *
     * @access public
     * @return mixed
     */
    public function mask($mode = null)
    {
        $this->files->mask((string)$this, $mode ?: 0755);
        return $this;
    }

    /**
     * flushes all files in the directory
     *
     * @access public
     * @return mixed
     */
    public function flush()
    {
        $this->files->flush((string)$this);
        return $this;
    }

    /**
     * check if directory is empty
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
     * Check if a files exists
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
     * Get a collection of files and firectories objects (SplFileInfo objects)
     * based on filters.
     *
     * @access public
     * @return FileCollection
     */
    public function get()
    {
        $collection = $this->getCollection($path = $this->getEntryPoint());

        if ($this->includeSelfFilter) {
            $collection->add($this->getFileInfo($path));
        }

        return $this->listDir($collection, $path, true, false);

    }

    /**
     * getFileInfo
     *
     * @param mixed $file
     *
     * @access public
     * @return SplFileInfo
     */
    public function getFileInfo($file = null)
    {
        $file = is_null($file) ? (string)$this : $this->getRealPath($file);

        $subpathname = $this->substitutePaths((string)$this, $file);
        $subpath     = dirname($subpathname);

        return new SplFileInfo($file, $subpath, $subpathname);
    }

    /**
     * Converts this directory to an array,
     * based on previously applied filters.
     *
     * @access public
     * @return array
     */
    public function toArray()
    {
        return $this->get()->toArray();
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
        if (!is_array($expression) && !is_string($expression)) {
            throw new \InvalidArgumentException('Argument 1 must be string or array');
        }

        $this->populateIgnoreFilter((array)$expression);
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
     * findIn
     *
     * @param mixed $subPath
     *
     * @access public
     * @return mixed
     */
    public function findIn($subPath = null)
    {
        $this->startAtFilter = $subPath;
        return $this;
    }

    /**
     * includeSelf
     *
     * @access public
     * @return mixed
     */
    public function includeSelf()
    {
        $this->includeSelfFilter = true;
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
        $this->setDotFilter();

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

        $count = true;

        foreach ($iterator = $this->getIterator($location) as $fileInfo) {

            // continue loop if file is link
            if ($fileInfo->isLink()) {
                continue;
            }
            // add file to collection:
            if ($fileInfo->isFile() && true !== $ignorefiles && $this->isIncludedFile($fileInfo->getBaseName())) {
                $collection->add($fileInfo);
                continue;
            }
            // operate on all included directories
            if ($fileInfo->isDir()) {
                // only add directory to collection only if onlyfiles is false
                // and is included directory
                if ($this->isIncludedDir($fileInfo->getRealPath())) {
                    if (true !== $this->onlyFilesFilter) {
                        $collection->add($fileInfo);
                    }
                    // just list this directory if max depths is not already
                    // reached
                    if (!$this->isRecursionStoppend()) {
                        $this->countRecursion($count);
                        $this->doList($collection, $fileInfo->getRealPath(), $recursive, $ignorefiles);
                    }
                }
            }
        }
    }

    /**
     * countRecursion
     *
     * @param mixed $count
     *
     * @access private
     * @return void
     */
    private function countRecursion(&$count)
    {
        if (isset($this->depthsFilter) && $count) {
            $count = false;
            $this->depthsFilter--;
        }
    }

    /**
     * isRecursionStoppend
     *
     * @access private
     * @return bool
     */
    private function isRecursionStoppend()
    {
        if (isset($this->depthsFilter)) {
            return $this->depthsFilter < 1;
        }
        return false;
    }

    private function getEntryPoint()
    {
        if (is_null($this->startAtFilter)) {
            return (string)$this;
        }

        return (string)$this.DIRECTORY_SEPARATOR.ltrim($this->startAtFilter, '\/');
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
        unset($this->startAtFilter);
        unset($this->currentFilter);
        unset($this->currentExclude);
        unset($this->includeSelfFilter);

        $this->inFilter          = null;
        $this->fileFilter        = null;
        $this->notInFilter       = null;
        $this->depthsFilter      = null;
        $this->ignoreFilter      = null;
        $this->startAtFilter     = null;
        $this->currentFilter     = null;
        $this->currentExclude    = null;
        $this->includeSelfFilter = null;

        $this->onlyFilesFilter   = false;
    }

    /**
     * setVcsFilter
     *
     * @access protected
     * @return void
     */
    protected function setVcsFilter()
    {
        $this->populateIgnoreFilter($this->ignoreVcs ? static::$vcsPattern : []);
    }

    /**
     * setDotFilter
     *
     * @access protected
     * @return void
     */
    protected function setDotFilter()
    {
        $this->populateIgnoreFilter($this->ignoreDot ? ['\..*?'] : []);
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
            FilesystemIterator::CURRENT_AS_FILEINFO|$flags,
            (string)$this
        );
        $iterator->setInfoClass(__NAMESPACE__.'\\SplFileInfo');

        return $iterator;
    }

    /**
     * create a new filecollection object
     *
     * @param string $path
     *
     * @access protected
     * @return FileCollection
     */
    protected function getCollection($path = null)
    {
        return new FileCollection($path);
    }

    /**
     * populateIgnoreFilter
     *
     * @param array $patterns
     *
     * @access private
     * @return void
     */
    private function populateIgnoreFilter(array $patterns)
    {
        if (!isset($this->ignoreFilter)) {
            $this->ignoreFilter = new FileFilter($patterns);
        } else {
            !empty($patterns) && $this->ignoreFilter->add($patterns);
        }
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
