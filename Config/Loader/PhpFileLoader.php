<?php

/**
 * This File is part of the Selene\Components\Config\Loader package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Config\Loader;

/**
 * @class PhpFileLoader
 * @package Selene\Components\Config\Loader
 * @version $Id$
 */
abstract class PhpFileLoader extends FileLoader
{
    /**
     * extension
     *
     * @var string
     */
    protected $extension = 'php';

    /**
     * {@inheritdoc}
     */
    protected function doLoad($resource)
    {
        return include $resource;
    }
}
