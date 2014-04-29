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


use \Selene\Components\DI\Parameters;
use \Selene\Components\DI\ContainerInterface;
use \Selene\Components\Config\ConfigurationInterface;
use \Symfony\Component\HttpKernel\HttpKernelInterface;
use \Selene\Components\Kernel\StackBuilder as KernelStackBuilder;

/**
 * @class PackageRepository
 *
 * @package Selene\Components\Package
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class PackageRepository implements PackageRepositoryInterface, \IteratorAggregate
{
    /**
     * packages
     *
     * @var array
     */
    protected $aliases;

    /**
     * packages
     *
     * @var array
     */
    protected $packages;

    /**
     * @param array $packages
     *
     * @access public
     * @return mixed
     */
    public function __construct(array $packages = [])
    {
        $this->aliases = [];
        $this->packages = [];

        $this->addPackages($packages);
    }

    /**
     * addPackage
     *
     * @param PackageInterface $package
     *
     * @access public
     * @return mixed
     */
    public function add(PackageInterface $package)
    {
        $this->aliases[$package->getName()] = $package->getAlias();
        $this->packages[$package->getAlias()] = $package;
    }

    /**
     * addPackages
     *
     * @param array $packages
     *
     * @access public
     * @return void
     */
    public function addPackages(array $packages)
    {
        foreach ($packages as $package) {
            $this->add($package);
        }
    }

    /**
     * has
     *
     * @param mixed $name
     *
     * @access public
     * @return boolean
     */
    public function has($name)
    {
        return isset($this->packages[$this->getAlias($name)]);
    }

    /**
     * get
     *
     * @param mixed $name
     *
     * @access public
     * @return PackageInterce
     */
    public function get($name)
    {
        if (!$this->has($name)) {
            return;
        }

        return $this->packages[$this->getAlias($name)];
    }

    /**
     * getAlias
     *
     * @param mixed $name
     *
     * @access public
     * @return string|null
     */
    public function getAlias($name)
    {
        return isset($this->aliases[$name]) ? $this->aliases[$name] :
            (in_array($name, $this->aliases) ? $name : null);
    }

    /**
     * get all packages
     *
     * @access public
     * @return array
     */
    public function all()
    {
        return $this->packages;
    }

    /**
     * Start the package build process.
     * Load package config and start package bulding.
     *
     * @access public
     * @return mixed
     */
    public function build(ContainerInterface $container)
    {
        foreach ($this->packages as $package) {
            $this->loadPackageConfig($container, $package);
            $this->buildPackage($container, $package);
        }
    }

    /**
     * Loads the specific confgiguration for a specific package.
     *
     * @param ContainerInterface $container
     * @param PackageInterce $package
     *
     * @access protected
     * @return void
     */
    protected function loadPackageConfig(ContainerInterface $container, PackageInterface $package)
    {
        if (!($config = $package->getConfiguration()) instanceof ConfigurationInterface) {
            return;
        }

        $container->addObjectResource($config);

        $parameters = $container->getParameters();
        $containerClass = (new \ReflectionObject($container))->getName();
        $packageContainer = new $container(new Parameters($parameters->getRaw()));

        if ($packageContainer->hasParameter($name = 'package:'.$package->getAlias())) {
            $values = $packageContainer->getParameter($name);
        } else {
            $values = [];
        }

        $config->load($packageContainer, $values);

        $container->merge($packageContainer);
    }

    /**
     * buildPackage
     *
     * @param ContainerInterface $container
     * @param PackageInterface $package
     *
     * @access protected
     * @return mixed
     */
    protected function buildPackage(ContainerInterface $container, PackageInterface $package)
    {
        $container->addFileResource($package->getMeta());
        return $package->build($container);
    }

    /**
     * bootPackages
     *
     * @access public
     * @return mixed
     */
    public function boot()
    {
        foreach ($this->packages as $package) {
            $package->boot();
        }
    }

    /**
     * shutDown
     *
     * @access public
     * @return void
     */
    public function shutDown()
    {
        foreach ($this->packages as $package) {
            $package->shutDown();
        }
    }

    /**
     * registerMiddleWares
     *
     * @access public
     * @return void
     */
    public function registerMiddleWares(KernelStackBuilder $builder)
    {
        //foreach ($this->packages as $package) {

        //    if (!$package->provides(PackageInterface::PROV_MIDDLEWARE)) {
        //        continue;
        //    }

        //    $app = $this->app->getKernel();
        //    $this->registerMiddleware($builder, $package->getMiddlewares($app));
        //}
    }

    /**
     * registerMiddleWare
     *
     * @param KernelStackBuilder $builder
     * @param mixed $middlewares
     *
     * @access protected
     * @return void
     */
    protected function registerMiddleWare(KernelStackBuilder $builder, $middlewares)
    {
        if (!is_array($middlewares)) {
            $middlewares = [$middlewares];
        }

        foreach ($middlewares as $middleware) {
            $builder->add($middleware);
        }
    }

    /**
     * getIterator
     *
     * @access public
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->packages);
    }
}
