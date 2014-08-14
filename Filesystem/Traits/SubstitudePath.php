<?php

/**
 * This File is part of the Selene\Module\Filesystem\Traits package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Filesystem\Traits;

/**
 * @class SubstitudePath
 * @package Selene\Module\Filesystem\Traits
 * @version $Id$
 */
trait SubstitudePath
{

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

        if (strcasecmp($root, $path) !== 0) {
            throw new \InvalidArgumentException('Root path does not contain current path');
        }

        $subPath = substr($current, strlen($root) + 1);
        return false === $subPath ? '' : $subPath;
    }
}
