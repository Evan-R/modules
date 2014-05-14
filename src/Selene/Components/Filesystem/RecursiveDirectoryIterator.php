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

use Selene\Components\Filesystem\Traits\IteratorTrait;

/**
 * @class RecursiveDirectoryIterator
 * @package Selene\Components\Filesystem
 * @version $Id$
 */
class RecursiveDirectoryIterator extends \RecursiveDirectoryIterator
{
    //use IteratorTrait {
        //IteratorTrait::getSubPath as private _getSubPath;
        //IteratorTrait::getSubPathname as private _getSubPathname;
    //}

    public function __construct($path, $flags, $rootpath = null)
    {
        if ($flags & (\FilesystemIterator::CURRENT_AS_SELF|\FilesystemIterator::CURRENT_AS_PATHNAME)) {
            throw new \InvalidArgumentException(
                sprintf('%s only supports FilesystemIterator::CURRENT_AS_FILEINFO', __CLASS__)
            );
        }

        parent::__construct($path, $flags);

    }
    //public function getSubPath()
    //{
        //return parent::getSubPath();
    //}

    //public function getSubPathname()
    //{
        //return parent::getSubPathname();
    //}
}
