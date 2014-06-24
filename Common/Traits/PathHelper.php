<?php

/**
 * This File is part of the Selene\Components\Common\Traits package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Common\Traits;

/**
 * @trait PathHelper
 *
 * @package Selene\Components\Common\Traits
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
trait PathHelper
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
        return strspn($file, '/\\', 0, 1) or null !== parse_url($file, PHP_URL_SCHEME);
    }
}
