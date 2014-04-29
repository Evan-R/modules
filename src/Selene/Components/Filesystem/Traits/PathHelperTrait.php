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
     * expandPath
     *
     * @param mixed $path
     *
     * @access public
     * @return string
     */
    public function expandPath($path)
    {

        $bits = explode(DIRECTORY_SEPARATOR, str_replace('\\/', DIRECTORY_SEPARATOR, $path));

        $p = [];
        while (count($bits)) {
            $part = array_pop($bits);
            if ('..' === $part) {
                array_pop($bits);
            } elseif('' !== $part) {
                $p[] = $part;
            }
        }

        return implode(DIRECTORY_SEPARATOR, array_reverse($p));
    }
}
