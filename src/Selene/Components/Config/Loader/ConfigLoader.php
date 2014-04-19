<?php

/**
 * This File is part of the Selene\Components\Config package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Config\Loader;

use \Selene\Components\DI\ContainerInterface;

/**
 * @abstract class ConfigLoader implements FileLoaderInterface
 * @see FileLoaderInterface
 * @abstract
 *
 * @package Selene\Components\Config
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
abstract class ConfigLoader implements FileLoaderInterface
{
    /**
     * container
     *
     * @var \Selene\Components\DI\ContainerInterface
     */
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
