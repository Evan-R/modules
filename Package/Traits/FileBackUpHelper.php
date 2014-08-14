<?php

/**
 * This File is part of the Selene\Module\Package package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Package\Traits;

use \Selene\Module\Filesystem\Filesystem;

/**
 * @class FileBackUpHelper
 * @package Selene\Module\Package
 * @version $Id$
 */
trait FileBackUpHelper
{

    /**
     * backupIfOverride
     *
     * @param Filesystem $fs
     * @param mixed $file
     * @param mixed $override
     *
     * @return boolean
     */
    public function backupIfOverride(Filesystem $fs, $file, $override)
    {
        if (!$fs->isFile($file)) {
            $fs->ensureDirectory(dirname($file));
        } elseif (false === $override) {
            return false;
        }

        $fs->backup($file);

        return true;
    }
}
