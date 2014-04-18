<?php

/**
 * This File is part of the Selene\Components\DI\Tests\Loader package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\DI\Loader;

use \Selene\Components\DI\ContainerInterface;
use \Selene\Components\Config\Loader\FileLoaderInterface;

abstract class ConfigLoader implements FileLoaderInterface
{
    protected $container;

    /**
     * @param ContainerInterface $container
     *
     * @access public
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
}
