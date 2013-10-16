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

/**
 * @class FilesystemIterator
 * @package Selene\Components\Filesystem
 * @version $Id$
 */
class FilesystemIterator extends \FilesystemIterator
{
    /**
     * currentPath
     *
     * @var string
     */
    private $currentPath;

    /**
     * rootPath
     *
     * @var string
     */
    private $rootPath;

    /**
     * subPath
     *
     * @var string
     */
    private $subPath;

    /**
     * __construct
     *
     * @param mixed $path
     * @param mixed $flags
     * @param mixed $rootpath
     *
     * @access public
     * @return mixed
     */
    public function __construct($path, $flags, $rootpath = null)
    {

        if ($flags & (\FilesystemIterator::CURRENT_AS_SELF|\FilesystemIterator::CURRENT_AS_PATHNAME)) {
            throw new \InvalidArgumentException(sprintf('%s only supports FilesystemIterator::CURRENT_AS_FILEINFO', __CLASS__));
        }

        $this->currentPath = $path;
        $this->setRootPath($rootpath);

        parent::__construct($path, $flags);
    }

    /**
     * current
     *
     * @access public
     * @return SplFileInfo
     */
    public function current()
    {
        $info = new SplFileInfo(parent::current()->getPathname(), $this->getSubPath(), $this->getSubPathname(parent::current()->getBasename()));
        return $info;
    }

    /**
     * getSubPath
     *
     * @access public
     * @return mixed
     */
    private function getSubPath()
    {
        return $this->subPath;
    }

    /**
     * getSubPathname
     *
     * @access public
     * @return mixed
     */
    private function getSubPathname($basename = null)
    {
        return rtrim($this->subPath.DIRECTORY_SEPARATOR.$basename, '\/');
    }

    /**
     * setRootPath
     *
     * @param mixed $path
     *
     * @access public
     * @return mixed
     */
    private function setRootPath($path)
    {
        $this->rootPath = $path;
        $this->substitutePaths($path, $this->currentPath);
    }

    /**
     * substitutePaths
     *
     * @param mixed $root
     * @param mixed $current
     *
     * @access private
     * @return mixed
     */
    private function substitutePaths($root, $current)
    {
        $path = substr($current, 0, strlen($root));

        if (strcasecmp($root, $path) !== 0)  {
            throw new \InvalidArgumentException('Root path does not contain current path');
        }

        $subPath = substr($current, strlen($root) + 1);
        $this->subPath = false === $subPath ? '' : $subPath;
    }
}
