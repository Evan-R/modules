<?php

/**
 * This File is part of the Selene\Module\Package package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Package;

use \Selene\Module\DI\BuilderInterface;
use \Selene\Adapter\Kernel\ApplicationInterface;
use \Selene\Adapter\Console\Application as Console;

/**
 * @interface PackageInterface
 *
 * @package Selene\Module\Package
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
     * @param \Selene\Module\DI\BuilderInterface $builder
     * @internal param \Selene\Module\Package\ContainerInterface $container
     *
     * @return void
     */
    public function build(BuilderInterface $builder);

    /**
     * boot up the package
     *
     * @param \Selene\Module\Kernel\ApplicationInterface $app
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
     * @param \Selene\Module\Console\Application $console
     * @return void
     */
    public function registerCommands(Console $console);

    /**
     * getConfiguration
     *
     * @return null|\Selene\Module\Config\ContainerInterface
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
