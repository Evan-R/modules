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

use \Selene\Components\Core\Application;
use \Selene\Components\DI\ContainerInterface;

/**
 * @class PackageRepository
 *
 * @package Selene\Components\Package
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class PackageRepository implements PackageRepositoryInterface
{
    /**
     * packages
     *
     * @var array
     */
    protected $packages;

    /**
     * lazyPackages
     *
     * @var array
     */
    protected $lazyPackages;

    /**
     * loadedPackages
     *
     * @var array
     */
    protected $loadedPackages;

    /**
     * @param Application $app
     *
     * @access public
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * getApplication
     *
     * @access public
     * @return Application
     */
    public function getApplication()
    {
        return $this->app;
    }

    /**
     * Adds a single package to the packages array.
     *
     * @param mixed $package
     *
     * @access public
     * @return void
     */
    public function add($package)
    {
        $this->packages[] = $package;
    }

    /**
     * Add an array of packages.
     *
     * @param array $packages
     *
     * @access public
     * @return void
     */
    public function addPackages(array $packages)
    {
        array_unshift($this->packages, $packages);
        $this->packages = call_user_func_array('array_push', $packages);
    }

    /**
     * get all packages
     *
     * @access public
     * @return array
     */
    public function all()
    {
        $this->packages = array_unique($this->packages);
        return $this->packages;
    }

    /**
     * Register packages
     *
     * @param ContainerAwareInterface $container
     *
     * @access public
     * @throws \RuntimeException
     * @return void
     */
    public function registerPackages(ContainerInterface $container)
    {
        foreach ($this->packages as $packageClass) {
            $this->doLoadPackage($package = new $class($this->getApplication()), $container);
        }
    }

    /**
     * registerLazyPackages
     *
     * @param ContainerInterface $container
     *
     * @access public
     * @return void
     */
    public function registerLazyPackages(ContainerInterface $container)
    {

    }

    /**
     * doLoadPackage
     *
     * @param PackageInterface $package
     *
     * @access protected
     * @return void
     */
    protected function doLoadPackage(PackageInterface $package, ContainerInterface $container)
    {
        $packageName = $package->getName();

        if (array_key_exists($packageName, $this->loadedPackages)) {
            throw new \RuntimeException(sprintf('Package %s is already registered', $name));
        }

        if (true) {
            $package->register($container);
        }

        $this->loadedPackages[$packageName] = $package;
    }

    /**
     * doRegisterPackage
     *
     * @param PackageInterface $package
     * @param ContainerInterface $container
     *
     * @access protected
     * @return mixed
     */
    protected function doRegisterPackage(PackageInterface $package, ContainerInterface $container)
    {
        $package->register($container);
    }

    /**
     * bootPackages
     *
     * @access public
     * @return mixed
     */
    public function bootPackages()
    {
        foreach ($this->loadedPackages as $package) {
            $package->boot();
        }
    }

    public function bootLazyPackages()
    {

    }
}
