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

use \Selene\Components\Common\Traits\Getter;
use \Selene\Components\DI\BuilderInterface;
use \Selene\Components\DI\Loader\PhpLoader;
use \Selene\Components\DI\Loader\XmlLoader;
use \Selene\Components\DI\Loader\CallableLoader;
use \Selene\Components\Config\Configuration;
use \Selene\Components\Config\Resource\Locator;
use \Selene\Components\Config\Resource\LoaderResolver;
use \Selene\Components\Config\Resource\LocatorInterface;
use \Selene\Components\Config\Resource\DelegatingLoader;
use \Selene\Components\Routing\Loader\PhpLoader as PhpRoutingLoader;
use \Selene\Components\Routing\Loader\XmlLoader as XmlRoutingLoader;
use \Selene\Components\Routing\Loader\CallableLoader as CallableRoutingLoader;
use \Selene\Components\Routing\RouteCollectionInterface as Routes;

/**
 * @abstract class PackageConfiguration extends BaseConfig
 * @see BaseConfig
 * @abstract
 *
 * @package Selene\Components\Package
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
abstract class PackageConfiguration extends Configuration
{
    use Getter;

    /**
     * package
     *
     * @var string
     */
    private $packagePath;

    /**
     * parameters
     *
     * @var \Selene\Components\DI\ParameterInterface
     */
    private $parameters;

    /**
     * Constructor.
     *
     * @param string $packagePath
     */
    public function __construct($packagePath)
    {
        $this->packagePath = $packagePath;
    }

    /**
     * @api
     * {@inheritdoc}
     */
    public function load(BuilderInterface $builder, array $values)
    {
        $this->parameters = $builder->getContainer()->getParameters();

        $this->setup($builder, $values);

        $this->parameters = null;
    }

    /**
     * Like Configuration::load().
     *
     * Unlike load(), in setup() you have direct access to the containers
     * parameters using getParameter($name, $default = null, $resolved).
     *
     * @param BuilderInterface $builder
     * @param array $values
     *
     * @api
     * @return void
     */
    public function setup(BuilderInterface $builder, array $values)
    {
    }

    /**
     * getParameter
     *
     * @param mixed $param
     * @param mixed $default
     * @param mixed $resolved
     *
     * @return mixed
     */
    protected function getParameter($param, $default = null, $resolved = false)
    {
        if ($this->parameters && $this->parameters->has($param)) {
            $value = $this->parameters->get($param);

            return $resolved ? $this->parameters->resolveParam($value) : $value;
        }

        return $resolved && $this->parameters ? $this->parameters->resolveParam($default) : $default;
    }

    /**
     * setParameter
     *
     * @param string $param
     * @param mixed  $value
     *
     * @return void
     */
    protected function setParameter($param, $value = null)
    {
        if ($this->parameters) {
            $this->parameters->set($param, $value);

            return;
        }

        throw new \LogicException(
            'Cannot set parameters. You can only use "setParameters()" only within the "setup()" method.'
        );
    }

    /**
     * getRoutingLoader
     *
     * @param mixed $param
     *
     * @return LoaderInterface
     */
    protected function getRoutingLoader(BuilderInterface $builder, Routes $routes, LocatorInterface $locator = null)
    {
        $locator = $locator ?: new Locator([$this->getResourcePath()]);

        return new DelegatingLoader(new LoaderResolver([
            new CallableRoutingLoader($builder, $locator, $routes),
            new PhpRoutingLoader($builder, $locator, $routes),
            new XmlRoutingLoader($builder, $locator, $routes)
        ]));
    }

    /**
     * getConfigLoader
     *
     * @param BuilderInterface $builder
     * @param LocatorInterface $locator
     *
     * @return LoaderInterface
     */
    protected function getConfigLoader(BuilderInterface $builder, LocatorInterface $locator = null)
    {
        $locator = $locator ?: new Locator([$this->getResourcePath()]);

        return new DelegatingLoader(new LoaderResolver([
            new CallableLoader($builder, $locator),
            new PhpLoader($builder, $locator),
            new XmlLoader($builder, $locator)
        ]));
    }

    /**
     * getResourcePath
     *
     * @return string
     */
    protected function getResourcePath()
    {
        return $this->getPackagePath().DIRECTORY_SEPARATOR.'Resources'.DIRECTORY_SEPARATOR.'config';
    }

    /**
     * getPackage
     *
     * @return string
     */
    protected function getPackagePath()
    {
        return $this->packagePath;
    }

    /**
     * Default merger for package config values.
     *
     * @param array $values
     *
     * @return array
     */
    protected function mergeValues(array $values)
    {
        $config = [];

        foreach ($values as $v) {
            $config = array_merge($config, $v);
        }

        return $config;
    }
}
