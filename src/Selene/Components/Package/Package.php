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

use \Selene\Components\Console\Application as Console;
use \Selene\Components\Kernel\Application;
use \Selene\Components\DI\ContainerInterface;
use \Selene\Components\DI\ContainerAwareInterface;
use \Selene\Components\DI\Loader\XmlLoader;
use \Selene\Components\DI\Loader\PhpLoader;
use \Selene\Components\DI\Loader\CallableLoader;
use \Selene\Components\DI\Traits\ContainerAwareTrait;
use \Selene\Components\Config\Loader\DelegatingLoader;
use \Selene\Components\Config\Loader\Resolver as LoaderResolver;
use \Selene\Components\Config\Locator\FileLocator;
use \Selene\Components\DI\BuilderInterface as ContainerBuilderInterface;

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
     * alias
     *
     * @var string
     */
    protected $alias;

    /**
     * setApplication
     *
     * @param Application $app
     *
     * @access public
     * @return mixed
     */
    public function setApplication(Application $app)
    {
        $this->app = $app;
    }

    /**
     * getExtension
     *
     *
     * @access public
     * @return mixed
     */
    public function getConfiguration()
    {
        $class = $this->getNamespace() . '\\Config\\Config';
        return new $class;
    }

    /**
     * getResourcePath
     *
     * @access public
     * @return mixed
     */
    public function getResourcePath()
    {
        return $this->getPath() . DIRECTORY_SEPARATOR . 'Resources';
    }

    /**
     * getAlias
     *
     * @access public
     * @return string
     */
    public function getAlias()
    {
        if (null === $this->alias) {
            $name = $this->getName();
            $base = 0 !== strrpos($name, 'Package') ? substr($name, 0, -strlen('Package')) : $name;

            $this->alias = strLowDash($base);
        }

        return $this->alias;
    }

    /**
     * build
     *
     * @param ContainerInterface $container
     *
     * @access public
     * @return void
     */
    public function build(ContainerBuilderInterface $builder)
    {
    }

    /**
     * Boot the package.
     *
     * @access public
     * @return void
     */
    public function boot()
    {
    }

    /**
     * shutdown
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
        return $this->getPath().DIRECTORY_SEPARATOR.'meta.xml';
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
     * registerCommands
     *
     * @access public
     * @return mixed
     */
    public function registerCommands(Console $console)
    {

    }

    /**
     * registerMiddleWares
     *
     * @access public
     * @return array|HttpKernelInterface
     */
    public function getMiddlewares($app)
    {

    }

    /**
     * supports
     *
     * @param mixed $type
     *
     * @access public
     * @return mixed
     */
    public function provides($type = null)
    {
        if (null !== $type) {
            return in_array($type, $this->getProviderTypes());
        }

        return $this->getProviderTypes();
    }

    /**
     * getSupportedTypes
     *
     * @access protected
     * @return mixed
     */
    protected function getProviderTypes()
    {
        return [];
    }
}
