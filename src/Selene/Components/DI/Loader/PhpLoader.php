<?php

/**
 * This File is part of the Selene\Components\DI\Loader package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\DI\Loader;

/**
 * @class PhpLoader
 * @package Selene\Components\DI\Loader
 * @version $Id$
 */
class PhpLoader extends ConfigLoader
{
    /**
     * load
     *
     * @param mixed $resource
     *
     * @access public
     * @return void
     */
    public function load($resource)
    {
        $container = $this->container;
        $container->addFileResource($resource);

        include $resource;
    }

    /**
     * supports
     *
     * @param mixed $format
     *
     * @access public
     * @return boolean
     */
    public function supports($format)
    {
        return 'php' === strtolower($format);
    }
}
