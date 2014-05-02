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

use \ArrayIterator;
use \IteratorAggregate;
use \Selene\Components\Common\Interfaces\JsonableInterface;
use \Selene\Components\Common\Interfaces\ArrayableInterface;

/**
 * @class FileCollection implements IteratorAggregate, ArrayableInterface, JsonableInterface
 * @see IteratorAggregate
 * @see ArrayableInterface
 * @see JsonableInterface
 *
 * @package Selene\Components\Filesystem
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class FileCollection implements IteratorAggregate, ArrayableInterface, JsonableInterface
{
    /**
     * pool
     *
     * @var arrray
     */
    protected $pool = [];

    /**
     * sort
     *
     * @var mixed
     */
    protected $sort;

    /**
     * nested
     *
     * @var bool
     */
    protected $nested = true;

    /**
     * pathAsKey
     *
     * @var mixed
     */
    protected $pathAsKey = true;

    /**
     * basedir
     *
     * @var mixed
     */
    protected $basedir;

    /**
     * dirKey
     *
     * @var string
     */
    protected static $dirKey  = '%directory%';

    /**
     * dirsKey
     *
     * @var string
     */
    protected static $dirsKey  = '%directories%';

    /**
     * fileKey
     *
     * @var string
     */
    protected static $fileKey = '%files%';

    /**
     * keyDelimmiter
     *
     * @var string
     */
    protected static $keyStartDelimmiter = '%';

    /**
     * keyDelimmiter
     *
     * @var string
     */
    protected static $keyEndDelimmiter = '%';

    /**
     * add
     *
     * @access public
     */
    public function __construct($basedir)
    {
        $this->baseDir = $basedir;
    }

    /**
     * getIterator
     *
     *
     * @access public
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->pool);
    }

    /**
     * add
     *
     * @access public
     * @return void
     */
    public function add(SplFileInfo $file)
    {
        if ($file->isFile()) {
            $this->pool[$this->getFilePath($file)] = $file;
        }
        if ($file->isDir()) {
            $this->pool[$this->getDirPath($file)] = $file;
        }
    }

    /**
     * normalizePath
     *
     * @param mixed $path
     *
     * @access protected
     * @return string
     */
    protected function normalizePath($path)
    {
        return str_replace('\\', '/', $path);
    }

    /**
     * expandPath
     *
     * @param mixed $path
     * @param SplFileInfo $file
     *
     * @access protected
     * @return void
     */
    protected function expandPath($path, SplFileInfo $file, array &$data)
    {
        arraySet($path, $file, $data, '/');
    }

    /**
     * getPath
     *
     * @param SplFileInfo $file
     *
     * @access protected
     * @return string
     */
    protected function getFilePath(SplFileInfo $file)
    {
        $path = $this->normalizePath(substr($file->getPathName(), strlen($this->baseDir)));
        $path = explode('/', $path);
        $name = array_pop($path);
        $path = implode('/'.static::$dirsKey.'/', $path).'/'.static::$fileKey.'/'.$name;

        return ltrim($path, '/');

    }

    /**
     * getDirPath
     *
     * @param SplFileInfo $file
     *
     * @access protected
     * @return string
     */
    protected function getDirPath(SplFileInfo $file)
    {
        $path = $this->normalizePath(substr($file->getPathName(), strlen($this->baseDir)));
        $path = explode('/', $path);
        $path = implode('/'.static::$dirsKey.'/', $path).'/'.static::$dirKey;

        return ltrim($path, '/');
    }

    /**
     * getPath
     *
     * @param SplFileInfo $file
     *
     * @access protected
     * @return string
     */
    public function getPath(SplFileInfo $file)
    {
        return ltrim($this->normalizePath(substr($file->getPathName(), strlen($this->baseDir))), '/');
    }

    /**
     * toArray
     *
     * @access public
     * @return array
     */
    public function toArray()
    {
        $in = [];
        $this->applySort();

        if ($this->nested) {
            foreach ($this->pool as $path => $file) {
                $this->expandPath($path, $file, $in);
            }
            return $this->exportToArray($in);
        }
        return $this->export($this->pool);
    }

    /**
     * applySort
     *
     * @access protected
     * @return void
     */
    protected function applySort()
    {
        if (isset($this->sort)) {
            list($sortMethod, $sortOptions) = $this->sort;
            call_user_func_array([$this, $sortMethod], (array)$sortOptions);
        } else {
            $this->doSort();
        }
    }


    /**
     * export
     *
     * @param array $array
     * @param array $out
     *
     * @access protected
     * @return array
     */
    protected function export(array $array, array $out = [])
    {
        foreach ($array as $path => $file) {
            $out[] = $file->getRealPath();
        }
        return $out;
    }

    /**
     * sortByModDate
     *
     * @param string $direction
     *
     * @access public
     * @return FileCollection
     */
    public function sortByModDate($direction = 'asc')
    {
        $this->sort = ['doSortByModDate', $direction];
        return $this;
    }

    /**
     * sortByName
     *
     * @param string $direction
     *
     * @access public
     * @return FileCollection
     */
    public function sortByName($direction = 'asc')
    {
        $this->sort = ['doSort', $direction];
        return $this;
    }

    /**
     * sortBySize
     *
     * @param string $direction
     *
     * @access public
     * @return FileCollection
     */
    public function sortBySize($direction = 'asc')
    {
        $this->sort = ['doSortBySize', $direction];
        return $this;
    }

    /**
     * sortByExtension
     *
     * @param string $direction
     *
     * @access public
     * @return void
     */
    public function sortByExtension($direction = 'asc')
    {
        $this->sort = ['doSortByExtension', $direction];
        return $this;
    }

    /**
     * doSort
     *
     * @param string $direction
     *
     * @access protected
     * @return void
     */
    protected function doSort($direction = 'asc')
    {
        ksort($this->pool);
        if ('desc' === $direction) {
            $this->pool = array_reverse($this->pool);
        }
    }

    /**
     * doSortByModDate
     *
     * @param string $direction
     *
     * @access protected
     * @return void
     */
    protected function doSortByModDate($direction = 'asc')
    {
        $less = 'asc' === $direction ? -1 : ('desc' === $direction ? 1 : 0);
        $more = 0 === $less ? 1 : -$less;
        uasort(
            $this->pool,
            function ($a, $b) use ($more, $less) {
                return $a->getMTime() <= $b->getMTime() ? $less : $more;
            }
        );
    }

    /**
     * doSortBySize
     *
     * @param string $direction
     *
     * @access protected
     * @return void
     */
    protected function doSortBySize($direction = 'asc')
    {
        $less = 'asc' === $direction ? -1 : ('desc' === $direction ? 1 : 0);
        $more = 0 === $less ? 1 : -$less;
        uasort(
            $this->pool,
            function ($a, $b) use ($more, $less) {
                return $a->getSize() <= $b->getSize() ? $less : $more;
            }
        );
    }

    /**
     * doSortByExtension
     *
     * @param string $direction
     *
     * @access protected
     * @return void
     */
    protected function doSortByExtension($direction = 'asc')
    {
        uasort(
            $this->pool,
            function ($a, $b) {
                return $a->getExtension() === $b->getExtension() ? 2 : 1;
            }
        );

        if ('desc' === $direction) {
            $this->pool = array_reverse($this->pool);
        }
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
     * setNestedOutput
     *
     * @param boolean $tree
     *
     * @access public
     * @return FileCollection
     */
    public function setOutputTree($tree = true, $pathAsKey = true)
    {
        $this->nested    = $tree;
        $this->pathAsKey = $pathAsKey;
        return $this;
    }

    /**
     * exportToArray
     *
     * @param array $in
     * @param array $out
     *
     * @access protected
     * @return array
     */
    protected function exportToArray(array $in, array $out = [])
    {
        //ksort($in);
        foreach ($in as $path => $file) {

            if (is_array($file)) {
                if ($this->pathAsKey or (static::$dirsKey === $path or static::$fileKey === $path)) {
                    $out[$path] = $this->exportToArray($file);
                } else {
                    $out[] = $this->exportToArray($file);
                }
                continue;
            }

            $name = $file->getFilename();

            if ($file->isDir()) {
                $out = array_merge($file->toArray(), $out);
                continue;
            }

            if ($file->isFile()) {
                if ($this->pathAsKey) {
                    $out[$name] = isset($out[$name]) ? array_merge($file->toArray(), $out) : $file->toArray();
                } else {
                    $out[] = isset($out[$name]) ? array_merge($file->toArray(), $out) : $file->toArray();
                }
                continue;
            }
        }
        return $out;
    }

    /**
     * getPool
     *
     *
     * @access public
     * @return array
     */
    public function getPool()
    {
        return $this->pool;
    }

    /**
     * setKeyDelimmiter
     *
     * @param mixed $key
     *
     * @access public
     * @return void
     */
    public static function setKeyStartDelimmiter($key)
    {
        static::$keyStartDelimmiter = $key;
    }

    /**
     * setKeyDelimmiter
     *
     * @param mixed $key
     *
     * @access public
     * @return void
     */
    public static function setKeyEndDelimmiter($key)
    {
        static::$keyEndDelimmiter = $key;
    }

    /**
     * setDirectoriesKey
     *
     * @param mixed $key
     *
     * @access public
     * @return void
     */
    public static function setDirectoriesKey($key)
    {
        static::$dirsKey = static::getKey($key);
    }

    /**
     * setDirectoryKey
     *
     * @param mixed $key
     *
     * @access public
     * @return void
     */
    public static function setDirectoryKey($key)
    {
        static::$dirKey = static::getKey($key);
    }

    /**
     * setFilesKey
     *
     * @param mixed $key
     *
     * @access public
     * @return void
     */
    public static function setFilesKey($key)
    {
        static::$fileKey = static::getKey($key);
    }

    /**
     * getKey
     *
     * @param mixed $key
     *
     * @access private
     * @return string
     */
    private static function getKey($key)
    {
        return sprintf('%s%s%s', static::$keyStartDelimmiter, $key, static::$keyEndDelimmiter);
    }
}
