<?php

/*
 * This File is part of the Selene\Module\Package package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Package;

use \Selene\Module\Common\Traits\Getter;
use \Selene\Module\DI\BuilderInterface;
use \Selene\Module\DI\Loader\PhpLoader;
use \Selene\Module\DI\Loader\XmlLoader;
use \Selene\Module\DI\Loader\CallableLoader;
use \Selene\Module\Config\Configuration;
use \Selene\Module\Config\Resource\Locator;
use \Selene\Module\Config\Loader\Resolver as LoaderResolver;
use \Selene\Module\Config\Loader\DelegatingLoader;
use \Selene\Module\Config\Validator\Nodes\RootNode;
use \Selene\Module\Config\Validator\Builder as ValidationBuilder;
use \Selene\Module\Config\Resource\LocatorInterface;
use \Selene\Module\Routing\Loader\DI\PhpLoader as PhpRoutingLoader;
use \Selene\Module\Routing\Loader\DI\XmlLoader as XmlRoutingLoader;
use \Selene\Module\Routing\Loader\DI\CallableLoader as CallableRoutingLoader;
use \Selene\Module\Routing\RouteCollectionInterface as Routes;
use \Selene\Module\Filesystem\Traits\PathHelperTrait;

/**
 * @abstract class PackageConfiguration extends BaseConfig
 * @see BaseConfig
 * @abstract
 *
 * @package Selene\Module\Package
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
abstract class PackageConfiguration extends Configuration
{
    use Getter,
        PathHelperTrait;

    /**
     * package
     *
     * @var string
     */
    private $packagePath;

    /**
     * packageAlias
     *
     * @var string
     */
    private $packageAlias;

    /**
     * parameters
     *
     * @var \Selene\Module\DI\ParameterInterface
     */
    private $parameters;

    /**
     * Constructor.
     *
     * @param string $packagePath
     */
    public function __construct(PackageInterface $package)
    {
        $this->packagePath  = $package->getPath();
        $this->packageAlias = $package->getAlias();
    }

    /**
     * @api
     * {@inheritdoc}
     */
    public function load(BuilderInterface $builder, array $values)
    {
        $this->parameters = $builder->getContainer()->getParameters();

        $this->loadResources($builder, $this->getResources());

        $this->setup($builder, $this->validate($this->mergeValues($values)));

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
     * configure
     *
     * @param RootNode $rootNode
     *
     * @return void
     */
    protected function configure(RootNode $rootNode)
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

    protected function resolveParameter($param)
    {
        if ($this->parameters) {
            return $this->parameters->resolveParam($param);
        }

        return $param;
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
     * Get the default config reousource to be loaded in the setup mmethod
     *
     * @return array
     */
    protected function getResources()
    {
        return ['services.xml'];
    }

    /**
     * loadResources
     *
     * @return void
     */
    protected function loadResources(BuilderInterface $builder, array $resources = [])
    {
        $loader = $this->getConfigLoader($builder);

        foreach ($resources as $resource) {
            $loader->load('services.xml');
        }
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
     * getPackageAlias
     *
     *
     * @access protected
     * @return mixed
     */
    protected function getPackageAlias()
    {
        return $this->packageAlias;
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
            $config = array_merge($config, (array)$v);
        }

        return $config;
    }

    /**
     * newValidatorBuilder
     *
     * @param string $name
     *
     * @return Builder
     */
    protected function newValidatorBuilder($name = null)
    {
        return new ValidationBuilder($name ?: $this->getPackageAlias());
    }
}
