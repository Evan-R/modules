<?php

/**
 * This File is part of the Selene\Module\Filesystem package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Filesystem\Traits;

use \Selene\Module\Filesystem\Exception\IOException;

/**
 * @trait FsHelperTrait
 *
 * @package Selene\Module\Filesystem
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
trait FsHelperTrait
{
    /**
     * mask
     *
     * @param mixed $cmask
     *
     * @access public
     * @return integer
     */
    public function mask($cmask)
    {
        return $cmask & ~umask();
    }

    /**
     * Ensures that the cachedir is writable.
     *
     * @api
     * @throws \Selene\Module\Filesystem\Exception\IOException
     * @throws \RuntimeException
     * @access public
     * @return ConfigCache
     */
    public function ensureWritable($dir)
    {
        if (is_writable($dir)) {
            return;
        }
            //try to set the directory to be writable.
        if (true !== @chmod($dir, $this->mask(0775))) {
            throw new \RuntimeException(sprintf('trying to write to directory %s but it\'s not writable', $dir));
        }
    }
}
