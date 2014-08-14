<?php

/**
 * This File is part of the Selene\Module\Config\Resource package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Config\Resource;

use \Selene\Module\Config\Loader\LoaderInterface;
use \Selene\Module\Filesystem\Traits\PathHelperTrait;

/**
 * @class Locator implements LocatorInterface
 * @see LocatorInterface
 *
 * @package Selene\Module\Config\Resource
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
class Locator implements LocatorInterface
{
    use PathHelperTrait;

    /**
     * paths
     *
     * @var array
     */
    protected $paths;

    /**
     * cwd
     *
     * @var string
     */
    protected $cwd;

    /**
     * Constructor.
     *
     * @param array  $paths
     * @param string $cwd
     */
    public function __construct(array $paths = [], $cwd = null)
    {
        $this->paths = $paths;
        $this->cwd   = $cwd ?: getcwd();
    }

    /**
     * {@inheritdoc}
     */
    public function locate($file, $collection = LoaderInterface::LOAD_ONE)
    {
        $files = [];

        foreach ($this->paths as $path) {

            if (!$resource = $this->locateResource($path, $file, $collection, $files)) {
                continue;
            }

            if (LoaderInterface::LOAD_ONE === $collection) {
                return $resource;
            }
        }

        return empty($files) && !$collection ? null : $files;
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function setPaths(array $paths)
    {
        $this->paths = [];

        foreach ($paths as $path) {
            $this->addPath($path);
        }
    }

    /**
     * locateResource
     *
     * @param string  $path
     * @param string  $file
     * @param boolean $collect
     * @param array   $collection
     *
     * @return void
     */
    protected function locateResource($path, $file, $collect = LoaderInterface::LOAD_ONE, array &$collection = [])
    {
        $dir = $this->getFullPath($path);

        if (!is_dir($dir)) {
            return false;
        }

        if (file_exists($resource = $dir . DIRECTORY_SEPARATOR . $file)) {

            if (LoaderInterface::LOAD_ONE === $collect) {
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
