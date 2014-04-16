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

use \Selene\Components\Kernel\Application;
use \Selene\Components\DI\ContainerInterface;
use \Selene\Components\DI\ContainerAwareInterface;
use \Selene\Components\DI\Traits\ContainerAwareTrait;

/**
 * @abstract class Package implements PackageInterface, ContainerAwareInterface
 * @see PackageInterface
 * @abstract
 *
 * @package Selene\Components\Package
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
abstract class Package implements PackageInterface, ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * app
     *
     * @var mixed
     */
    private $app;

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
     * @param AppCoreInterface $app
     *
     * @access public
     * @final
     * @return mixed
     */
    final public function __construct(Application $app = null)
    {
        $this->app = $app;
    }

    /**
     * register
     *
     * @param ContainerInterface $container
     *
     * @access public
     * @return void
     */
    public function register(ContainerInterface $container)
    {
    }

    /**
     * Boot the package.
     *
     * @access public
     * @return void
     */
    public function boot(ContainerInterface $container)
    {
    }

    /**
     * shutdown
     *
     * @access public
     * @return void
     */
    public function shutdown(ContainerInterface $container)
    {
    }

    /**
     * getNamespace
     *
     * @access public
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
     * getName
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
     * getPath
     *
     * @access public
     * @return mixed
     */
    final public function getPath()
    {
        if (null === $this->path) {
            $this->path = dirname($this->getPackageReflection()->getFileName());
        }

        return $this->path;
    }

    /**
     * getPackageReflection
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
     * isLazy
     *
     * @access public
     * @return mixed
     */
    public static function isLazy()
    {
        return static::$lazy;
    }

    /**
     * registerCommands
     *
     * @access public
     * @return mixed
     */
    public function registerCommands()
    {

    }
}
