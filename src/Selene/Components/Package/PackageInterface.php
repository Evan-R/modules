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
use \Selene\Components\Console\Application as Console;
use \Selene\Components\DI\BuilderInterface as ContainerBuilderInterface;

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

    /** @var string */
    const PROV_COMMAND = 'commands';

    /** @var string */
    const PROV_MIDDLEWARE = 'middleware';

    /**
     * build
     *
     * @param ContainerInterface $container
     *
     * @access public
     * @return mixed
     */
    public function build(ContainerBuilderInterface $builder);

    /**
     * boot up the package
     *
     * @access public
     * @return void
     */
    public function boot();

    /**
     * bundle shutdown.
     *
     * This is the place to put the shutdown Callbacks.
     *
     * @access public
     * @return void
     */
    public function shutdown();

    /**
     * registerCommands
     *
     * @access public
     * @return mixed
     */
    public function registerCommands(Console $console);

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
     * @return string
     */
    public function getName();

    /**
     * getAlias
     *
     * @access public
     * @return string
     */
    public function getAlias();

    /**
     * getRequirement
     *
     * @access public
     * @return string|boolean
     */
    public function getRequirement();

    /**
     * getNamespace
     *
     * @access public
     * @return mixed
     */
    public function getNamespace();
}
