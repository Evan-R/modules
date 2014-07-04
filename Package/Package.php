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
use \Selene\Components\Common\Helper\StringHelper;
use \Selene\Components\Console\Application as Console;

/**
 * The package class is an entry point for extending functionality of the
 * selene components in a framework context.
 *
 * @abstract class Package implements PackageInterface
 * @see PackageInterface
 * @abstract
 *
 * @package Selene\Components\Package
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
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
     * Return a package alias that is required by this package.
     *
     * @access public
     * @return string|boolean the parent package alias as string,
     *  otherwise boolean `false`
     */
    public function getRequirement()
    {
        return false;
    }

    /**
     * Provides a PackageConfiguration instance.
     *
     * If no additional Configuration is required for a package, this method
     * should return `null` or `false`.
     *
     * @access public
     * @return null|PackageConfiguration
     */
    public function getConfiguration()
    {
        $class = $this->getNamespace() . '\\Config\\Config';
        return new $class($this->getPath());
    }

    /**
     * Return the path to the resources of the package.
     *
     * Resources are flat files meant to scaffold configuration, routing, or
     * static assets.
     *
     * @access public
     * @return mixed
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
     * @access public
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
     * @param \Selene\Components\DI\BuilderInterface $builder
     * @internal param \Selene\Components\DI\BuilderInterface $container
     *
     * @access public
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
     * @access public
     * @param \Selene\Components\Kernel\ApplicationInterface $app
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
     * @access public
     * @return void
     */
    public function shutdown()
    {
    }

    /**
     * getMeta
     *
     * @access public
     * @return string
     */
    public function getMeta()
    {
        return $this->getPath().DIRECTORY_SEPARATOR.'package.xml';
    }

    /**
     * Get the namespace of this package.
     *
     * @access public
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
     * @access public
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
     * @access public
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
     * @access protected
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
     * @param \Selene\Components\Console\Application $console
     *
     * @access public
     * @return void
     */
    public function registerCommands(Console $console)
    {
    }
}
