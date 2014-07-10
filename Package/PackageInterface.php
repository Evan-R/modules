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

use \Selene\Components\DI\BuilderInterface;
use \Selene\Components\Kernel\ApplicationInterface;
use \Selene\Components\Console\Application as Console;

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
     * @param \Selene\Components\DI\BuilderInterface $builder
     * @internal param \Selene\Components\Package\ContainerInterface $container
     *
     * @return void
     */
    public function build(BuilderInterface $builder);

    /**
     * boot up the package
     *
     * @param \Selene\Components\Kernel\ApplicationInterface $app
     * @return void
     */
    public function boot(ApplicationInterface $app);

    /**
     * bundle shutdown.
     *
     * This is the place to put the shutdown Callbacks.
     *
     * @return void
     */
    public function shutdown();

    /**
     * registerCommands
     *
     * @param \Selene\Components\Console\Application $console
     * @return void
     */
    public function registerCommands(Console $console);

    /**
     * getConfiguration
     *
     * @return null|\Selene\Components\Config\ContainerInterface
     */
    public function getConfiguration();

    /**
     * getResourcePath
     *
     * @return string
     */
    public function getResourcePath();

    /**
     * getNamespace
     *
     * @return string
     */
    public function getPath();

    /**
     * getName
     *
     * @return string
     */
    public function getName();

    /**
     * getAlias
     *
     * @return string
     */
    public function getAlias();

    /**
     * getNamespace
     *
     * @return string
     */
    public function getNamespace();

    /**
     * requires
     *
     * @return array
     */
    public function requires();
}
