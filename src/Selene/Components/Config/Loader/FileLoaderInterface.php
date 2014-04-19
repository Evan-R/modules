<?php

/**
 * This File is part of the Selene\Components\Config\Loaders package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Config\Loader;

/**
 * @class FileLoaderInterface
 * @package
 * @version $Id$
 */
interface FileLoaderInterface
{
    public function load($resource);

    /**
     * Check if the loader supports the resource format.
     *
     * @param mixed $format
     *
     * @access public
     * @return boolean
     */
    public function supports($resource);
}
