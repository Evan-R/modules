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
use \Selene\Components\DI\BuilderInterface as ContainerBuilderInterface;
use \Selene\Components\DI\Builder as ContainerBuilder;

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

    protected $built;

    protected $sorted;

    protected $configLoader;

    /**
     * @param array $packages
     *
     * @access public
     * @return mixed
     */
    public function __construct(array $packages = [])
    {
        $this->sorted = false;
        $this->aliases = [];
        $this->packages = [];
        $this->built = [];

        $this->dependecies = new DependencyManager($this);

        $this->addPackages($packages);
        $this->configLoader = new ConfigLoader;
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
        $this->sorted = false;

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
     * @return void
     */
    public function build(ContainerBuilderInterface $builder)
    {
        $packages = $this->getSorted();

        foreach ($packages as &$package) {

            if (in_array($alias = $package->getAlias(), $this->built)) {
                continue;
            }

            //$this->loadPackageConfig($builder, $package);
            $this->configLoader->load($builder, $package);
            $this->buildPackage($builder, $package);

            $this->built[] = $alias;
        }

        $this->configLoader->unload();
    }

    protected function packageHasDependecies(PackageInterface $package)
    {
        return (bool)$package->requires();
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
     * getIterator
     *
     * @access public
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->packages);
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
    protected function loadPackageConfig(ContainerBuilderInterface $builder, PackageInterface $package)
    {
        if (!($config = $package->getConfiguration()) instanceof ConfigurationInterface) {
            return;
        }

        $container = $builder->getContainer();

        $parameters = $container->getParameters();
        $containerClass = (new \ReflectionObject($container))->getName();
        $packageContainer = new $container;
        $packageContainer->getParameters()->merge(new Parameters($parameters->getRaw()));

        $builder->replaceContainer($packageContainer);

        $config->load($builder, $builder->getPackageConfig($package->getAlias()));

        $container->merge($packageContainer);

        $builder->replaceContainer($container);
        $builder->addObjectResource($config);
        $builder->addObjectResource($package);
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
    protected function buildPackage(ContainerBuilderInterface $builder, PackageInterface $package)
    {
        //$builder->addFileResource($package->getMeta());
        return $package->build($builder);
    }

    /**
     * getSorted
     *
     * @return array
     */
    protected function getSorted()
    {
        if (!$this->sorted) {
            $this->sorted = true;
            $this->packages = $this->dependecies->getSorted();
        }

        return $this->packages;
    }
}
