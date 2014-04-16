<?php

/**
 * This File is part of the Selene\Adapters\Twig\Loaders package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Adapters\Twig\Loaders;

use \Twig_LoaderInterface as LoaderInterface;

/**
 * @class FileLoader
 * @package Selene\Adapters\Twig\Loaders
 * @version $Id$
 */
class FileLoader implements LoaderInterface
{
    public function getSource($source)
    {
        return $source;
    }

    public function getCacheKey($name)
    {
        return $name;
    }

    public function isFresh($name, $time)
    {
        return true;
    }
}
