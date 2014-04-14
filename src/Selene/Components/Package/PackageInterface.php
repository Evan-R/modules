<?php

/**
 * This File is part of the Selene\Components\Package package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Package;

use \Selene\Components\DI\ContainerInterface;

/**
 * @interface PackageInterface
 *
 * @package Selene\Components\Package
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
interface PackageInterface
{

    /**
     * Registers components and serivces on the DI container.
     *
     * @param ContainerInterface $container
     *
     * @access public
     * @return void
     */
    public function register(ContainerInterface $container);

    /**
     * boot up the package
     *
     * @access public
     * @return void
     */
    public function boot(ContainerInterface $container);

    /**
     * bundle shutdown.
     *
     * This is the place to put the shutdown Callbacks.
     *
     * @access public
     * @return void
     */
    public function shutdown(ContainerInterface $container);

    /**
     * registerCommands
     *
     * @access public
     * @return mixed
     */
    public function registerCommands();

    /**
     * getNamespace
     *
     * @access public
     * @return string
     */
    public function getPath();

    /**
     * getName
     *
     * @access public
     * @return mixed
     */
    public function getName();

    /**
     * getNamespace
     *
     * @access public
     * @return mixed
     */
    public function getNamespace();

    /**
     * isLazy
     *
     * @access public
     * @return boolean
     */
    public static function isLazy();
}
