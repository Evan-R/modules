<?php

/**
 * This File is part of the Selene\Components\Filesystem package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Filesystem\Traits;

/**
 * @trait PathHelperTrait
 *
 * @package Selene\Components\Filesystem
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
trait PathHelperTrait
{
    /**
     * isRelativePath
     *
     * @param mixed $file
     *
     * @access public
     * @return bool
     */
    public function isRelativePath($file)
    {
        return !$this->isAbsolutePath($file);
    }

    /**
     * isAbsolutePath
     *
     * @param mixed $file
     *
     * @access public
     * @return bool
     */
    public function isAbsolutePath($file)
    {
        return strspn($file, '/\\', 0, 1) || null !== parse_url($file, PHP_URL_SCHEME);
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
    public function substitutePaths($root, $current)
    {
        $path = substr($current, 0, strlen($root));

        if (strcasecmp($root, $path) !== 0) {
            throw new \InvalidArgumentException('Root path does not contain current path');
        }

        $subPath = substr($current, strlen($root) + 1);
        return false === $subPath ? '' : $subPath;
    }

    /**
     * expandPath
     *
     * @param mixed $path
     *
     * @access public
     * @return string
     */
    public function expandPath($path)
    {
        $prefix = $this->isAbsolutePath($path) ? DIRECTORY_SEPARATOR : '';

        $bits = explode(DIRECTORY_SEPARATOR, str_replace('\\/', DIRECTORY_SEPARATOR, $path));

        $p = [];

        $skip = 0;

        while (count($bits)) {

            $part = array_pop($bits);

            if (0 === strcmp($part, '..')) {
                $skip++;
                continue;
            }

            if (0 < $skip) {
                $skip--;
                continue;
            }

            if ('' !== $part) {
                $p[] = $part;
            }
        }

        return $prefix . trim(implode(DIRECTORY_SEPARATOR, array_reverse($p)), DIRECTORY_SEPARATOR);
    }
}
