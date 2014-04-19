<?php

/**
 * This File is part of the Selene\Components\DI package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\DI\Loader;

use \Selene\Components\Config\Loader\ConfigLoader;

/**
 * @class PhpLoader extends ConfigLoader
 * @see ConfigLoader
 *
 * @package Selene\Components\DI
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class PhpLoader extends ConfigLoader
{
    /**
     * Load a php file resource.
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
     * {@inheritdoc}
     * @param string $format
     */
    public function supports($format)
    {
        return 'php' === strtolower($format);
    }
}
