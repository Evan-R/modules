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
    /**
     * getSource
     *
     * @param mixed $source
     *
     * @access public
     * @return mixed
     */
    public function getSource($source)
    {
        return file_get_contents($source);
    }

    /**
     * getCacheKey
     *
     * @param mixed $name
     *
     * @access public
     * @return mixed
     */
    public function getCacheKey($name)
    {
        $key = hash('sha256', $name);
        return $key;
    }

    /**
     * isFresh
     *
     * @param mixed $name
     * @param mixed $time
     *
     * @access public
     * @return mixed
     */
    public function isFresh($name, $time)
    {
        return filemtime($name) < $time;
    }
}
