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

use \Selene\Components\FileSystem\Traits\PathHelperTrait;

/**
 * @class Locator implements LocatorInterface
 * @see LocatorInterface
 *
 * @package Selene\Components\Config\Resource
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class Locator implements LocatorInterface
{
    use PathHelperTrait;

    /**
     * @param array $paths
     *
     * @access public
     */
    public function __construct(array $paths = [], $cwd = null)
    {
        $this->paths = $paths;
        $this->cwd   = $cwd ?: getcwd();
    }

    /**
     * {@inheritdoc}
     */
    public function locate($file, $collection = false)
    {
        $files = [];

        foreach ($this->paths as $path) {

            if (!($resource = $this->locateResource($path, $file, $collection, $files))) {
                continue;
            }

            if (true !== $collection) {
                return $resource;
            }
        }

        return empty($files) && !$collection ? null : $files;
    }

    /**
     * setRootPath
     *
     * @param mixed $root
     *
     * @access public
     * @return void
     */
    public function setRootPath($root)
    {
        if (!is_dir($root)) {
            throw new \InvalidArgumentException(sprintf('%s is not a directory', $root));
        }
        $this->cwd = $root;
    }

    /**
     * {@inheritdoc}
     */
    public function addPath($path)
    {
        if (in_array($path, $this->paths)) {
            return;
        }

        $this->paths[] = $path;
    }

    /**
     * {@inheritdoc}
     */
    public function addPaths(array $paths)
    {
        foreach ($paths as $path) {
            $this->addPath($path);
        }
    }

    /**
     * locateResource
     *
     * @param mixed $path
     * @param mixed $collect
     * @param array $collection
     *
     * @access protected
     * @return mixed
     */
    protected function locateResource($path, $file, $collect = false, array &$collection = [])
    {
        $dir = $this->getFullPath($path);

        if (!is_dir($dir)) {
            return false;
        }

        if (file_exists($resource = $dir . DIRECTORY_SEPARATOR . $file)) {

            if (!$collect) {
                return $resource;
            }

            $collection[] = $resource;
        }
    }

    /**
     * getFullPath
     *
     * @param mixed $dir
     *
     * @access protected
     * @return string
     */
    protected function getFullPath($dir)
    {
        return $this->isRelativePath($dir) ? $this->cwd . DIRECTORY_SEPARATOR . $dir : $dir;
    }
}
