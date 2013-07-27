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

use ArrayIterator;
use IteratorAggregate;
use Selene\Components\Common\Interfaces\JsonableInterface;
use Selene\Components\Common\Interfaces\ArrayableInterface;

/**
 * @class FileCollection
 * @package
 * @version $Id$
 */
class FileCollection implements IteratorAggregate, ArrayableInterface, JsonableInterface
{
    /**
     * pool
     *
     * @var arrray
     */
    protected $pool = [];

    protected $basedir;

    /**
     * add
     *
     * @access public
     * @return mixed
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
     * @return mixed
     */
    public function getIterator()
    {
        return new ArrayIterator(isset($this->pool['.']) ? $this->pool['.'] : $this->pool);
    }

    /**
     * add
     *
     * @access public
     * @return mixed
     */
    public function add(SplFileInfo $file)
    {
        $this->expandPath($this->getPath($file), $file);
    }

    /**
     * getPath
     *
     * @param SplFileInfo $file
     *
     * @access protected
     * @return mixed
     */
    protected function getPath(SplFileInfo $file)
    {
        return '.'.str_replace('\\\/', '/', substr($file->getPathName(), strlen($this->baseDir)));
    }

    /**
     * expandPath
     *
     * @param mixed $path
     *
     * @access protected
     * @return mixed
     */
    protected function expandPath($path, SplFileInfo $file)
    {
        array_set($path, $file, $this->pool, $separator = '/');
    }
    /**
     * toArray
     *
     * @access public
     * @return array
     */
    public function toArray()
    {
        return $this->exportToArray(isset($this->pool['.']) ? $this->pool['.'] : $this->pool);
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
        foreach ($in as $file => $object) {
            if (is_array($object)) {
                $out['directories'][$file] = $this->exportToArray($object);
            } else {
                $out['files'][$file] = $object->toArray();
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
     * toJson
     *
     * @access public
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->toArray(), defined('JSON_PRETTY_PRINT') ? JSON_PRETTY_PRINT : 0);
    }
}
