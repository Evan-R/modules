<?php

/**
 * This File is part of the Selene\Components\Config\Locators package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Config\Locators;

use \Selene\Components\Common\Traits\PathHelper;

/**
 * @class FileLocator
 * @package Selene\Components\Config\Locators
 * @version $Id$
 */
class FileLocator implements LocatorInterface
{
    use PathHelper {
        PathHelper::isAbsolutePath as private isAbsPath;
        PathHelper::isRelativePath as private isRelPath;
    }

    /**
     * @param array $locations
     *
     * @access public
     */
    public function __construct(array $locations = [])
    {
        $this->cwd = getcwd();
        $this->locations = $locations;
    }

    /**
     * @param mixed $file
     * @param mixed $collect
     *
     * @access public
     * @return string|array
     */
    public function locate($file, $collect = true)
    {
        $files = [];

        foreach ($this->locations as $dir) {

            $dir = $this->isRelPath($dir) ? $this->cwd . DIRECTORY_SEPARATOR . $dir : $dir;

            if (!is_dir($dir)) {
                throw new \InvalidArgumentException(
                    sprintf('%s is not a directory', $dir)
                );
            }

            if (file_exists($resource = $dir . DIRECTORY_SEPARATOR . $file)) {

                if (true !== $collect) {
                    return $resource;
                }

                $files[] = $resource;
            }
        }
        return $files;
    }
}
