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
use \Selene\Module\Common\Helper\StringHelper;
use \Selene\Adapter\Kernel\ApplicationInterface;
use \Selene\Adapter\Console\Application as Console;

/**
 * The package class is an entry point for extending functionality of the
 * selene components in a framework context.
 *
 * @abstract class Package implements PackageInterface
 * @see PackageInterface
 * @abstract
 *
 * @package Selene\Module\Package
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
abstract class Package implements PackageInterface
{
    /**
     * path
     *
     * @var string
     */
    private $path;

    /**
     * name
     *
     * @var string
     */
    private $name;

    /**
     * namespace
     *
     * @var string
     */
    private $namespace;

    /**
     * packageReflection
     *
     * @var \ReflectionObject
     */
    private $packageReflection;

    /**
     * lazy
     *
     * @var mixed
     */
    protected static $lazy = false;

    /**
     * alias
     *
     * @var string
     */
    protected $alias;

    /**
     * Provides a PackageConfiguration instance.
     *
     * If no additional Configuration is required for a package, this method
     * should return `null` or `false`.
     *
     * @return null|PackageConfiguration
     */
    public function getConfiguration()
    {
        $class = sprintf('%s\%s', $this->getNamespace(), $this->getConfigClassName());

        return new $class($this);
    }

    /**
     * Return the path to the resources of the package.
     *
     * Resources are flat files meant to scaffold configuration, routing, or
     * static assets.
     *
     * @return string
     */
    public function getResourcePath()
    {
        return $this->getPath() . DIRECTORY_SEPARATOR . 'Resources';
    }

    /**
     * Get the package alias.
     *
     * The Alias is the snake cased version of your package name omitting the
     * `Package` suffix
     *
     * @return string
     */
    public function getAlias()
    {
        if (null === $this->alias) {
            $name = $this->getName();
            $base = 0 !== strripos($name, 'package') ? substr($name, 0, -7) : $name;

            $this->alias = StringHelper::strLowDash($base);
        }

        return $this->alias;
    }

    /**
     * Build package dependencies.
     *
     * This method is called only once before the service container is built.
     *
     * @param \Selene\Module\DI\BuilderInterface $builder
     * @internal param \Selene\Module\DI\BuilderInterface $container
     *
     * @return void
     */
    public function build(BuilderInterface $builder)
    {
    }

    /**
     * Boot the package.
     *
     * Provice additional bootstrapping for a package.
     *
     * @param \Selene\Module\Kernel\ApplicationInterface $app
     * @return void
     */
    public function boot(ApplicationInterface $app)
    {
    }

    /**
     * Define an array of packages that are required by this package.
     *
     * @return null|array
     */
    public function requires()
    {
    }

    /**
     * Shutdown ops on this package.
     *
     * @return void
     */
    public function shutdown()
    {
    }

    /**
     * Get the namespace of this package.
     *
     * @final
     * @return string
     */
    final public function getNamespace()
    {
        if (null === $this->namespace) {
            $this->namespace = $this->getPackageReflection()->getNamespaceName();
        }

        return $this->namespace;
    }

    /**
     * Get the package name.
     *
     * The default package name is the short name of the package class,
     * e.g. `Acme\Special\FooPackage` will result in `FooPackage`.
     *
     * @return string
     */
    final public function getName()
    {
        if (null === $this->name) {
            $this->name = $this->getPackageReflection()->getShortName();
        }

        return $this->name;
    }

    /**
     * Get the actual filesystem path of this package.
     *
     * @return string
     */
    final public function getPath()
    {
        if (null === $this->path) {
            $this->path = dirname($this->getPackageReflection()->getFileName());
        }

        return $this->path;
    }

    /**
     * Get the reflection object of this package.
     *
     * @final
     * @return \ReflectionObject
     */
    final public function getPackageReflection()
    {
        if (null === $this->packageReflection) {
            $this->packageReflection = new \ReflectionObject($this);
        }

        return $this->packageReflection;
    }

    /**
     * Register Console commands for this package.
     *
     * This method is only invoked when booting the application in cli mode.
     *
     * @param \Selene\Module\Console\Application $console
     *
     * @return void
     */
    public function registerCommands(Console $console)
    {
    }

    /**
     * getConfigClassName
     *
     * @return string
     */
    protected function getConfigClassName()
    {
        return 'Config';
    }
}
