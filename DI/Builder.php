<?php

/*
 * This File is part of the Selene\Module\DI package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\DI;

use \Selene\Module\Common\Traits\Getter;
use \Selene\Module\DI\Processor\Processor;
use \Selene\Module\DI\Processor\Configuration;
use \Selene\Module\DI\Processor\ProcessorDecorator;
use \Selene\Module\DI\Processor\ProcessorInterface;
use \Selene\Module\Config\Resource\FileResource;
use \Selene\Module\Config\Resource\ObjectResource;
use \Selene\Module\Config\Resource\Collector;
use \Selene\Module\Config\Resource\CollectorInterface;
use \Selene\Module\Config\Loader\LoaderInterface;

/**
 * @class Builder implements BuilderInterface
 * @see BuilderInterface
 *
 * @package Selene\Module\DI
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class Builder implements BuilderInterface
{
    use Getter;

    /**
     * processor
     *
     * @var \Selene\Module\DI\Processor\ProcessorInterface
     */
    protected $processor;

    /**
     * processor
     *
     * @var \Selene\Module\DI\Processor\ProcessorInterface
     */
    protected $processorDecorator;

    /**
     * container
     *
     * @var \Selene\Module\DI\ContainerInterface
     */
    protected $container;

    /**
     * resources
     *
     * @var array
     */
    protected $resources;

    /**
     * extensions
     *
     * @var array
     */
    protected $packages;

    /**
     * Create a new Builder instance.
     *
     * @param ContainerInterface $container the container to build.
     * @param ProcessorInterface $proc      the processor
     * @param array              $resources resource
     */
    public function __construct(
        ContainerInterface $container,
        ProcessorInterface $proc = null,
        CollectorInterface $resources = null
    ) {
        $this->container = $container;
        $this->processor = $proc ?: new Processor(new Configuration);
        $this->resources = $resources ?: new Collector;

        $this->packages  = [];
    }

    /**
     * Get the current DI Container
     *
     * @return \Selene\Module\DI\ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Replaces the current DI Container.
     *
     * @param \Selene\Module\DI\ContainerInterface $container a new
     * container.
     *
     * @return void
     */
    public function replaceContainer(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Merges two builder instances, including resources.
     *
     * This will merge the containers, resources, configs and parameters.
     *
     * @param \Selene\Module\DI\BuilderInterface $builder
     *
     * @return void
     */
    public function merge(BuilderInterface $builder)
    {
        $this->mergePackageConfigs($builder);
        $this->mergeResources($builder);

        $this->container->merge($builder->getContainer());
    }

    /**
     * Get the current Container Processor.
     *
     * Instead of returning the actual processor,
     * return a processor decorator.
     *
     * @return \Selene\Module\DI\Processor\ProcessorInterface
     */
    public function getProcessor()
    {
        return $this->processorDecorator ? $this->processorDecorator :
            $this->processorDecorator = new ProcessorDecorator($this->processor);
    }

    /**
     * Starts Building the DI Container.
     *
     * @return void
     */
    public function build()
    {
        $this->container->getParameters()->resolve()->all();

        $this->processor->process($this->container);

        $this->container->getParameters()->resolve()->all();
    }

    /**
     * Add a file resource to track.
     *
     * @param string $file Path to a file
     *
     * @return void
     */
    public function addFileResource($file)
    {
        $this->resources->addFileResource($file);
    }

    /**
     * Add an object resource to track.
     *
     * @param object $object any serilizable objet.
     *
     * @return void
     */
    public function addObjectResource($object)
    {
        $this->resources->addObjectResource($object);
    }

    /**
     * Dump all tracked resources.
     *
     * @return array
     */
    public function getResources()
    {
        return $this->resources->getResources();
    }

    /**
     * addPackageConfig
     *
     * @param mixed $package
     * @param array $config
     *
     * @return void
     */
    public function addPackageConfig($package, array $config)
    {
        $this->packages[$package][] = $config;
    }

    /**
     * Get all config arrays for a given extension id.
     *
     * @param string $extension the extension id.
     *
     * @return array
     */
    public function getPackageConfig($package)
    {
        return $this->getDefault($this->packages, $package, []);
    }

    /**
     * Get all extension config arrays.
     *
     * @return array
     */
    public function getPackageConfigs()
    {
        return $this->packages;
    }

    /**
     * Forward member calls to the container.
     *
     * @param string $method
     * @param array $arguments
     *
     * @throws \BadMethodCallException if the method does not exist.
     *
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        if (method_exists($this->container, $method)) {
            return call_user_func_array([$this->container, $method], $arguments);
        }

        throw new \BadMethodCallException(
            sprintf('call to undefined method %s::%s()', get_class($this), $method)
        );
    }

    /**
     * Merge extension configs.
     *
     * @param \Selene\Module\DI\BuilderInterface $builder
     *
     * @return void
     */
    protected function mergePackageConfigs(BuilderInterface $builder)
    {
        foreach ($builder->getPackageConfigs() as $package => $config) {
            $this->packages[$package] = array_merge($this->getPackageConfig($package), $config);
        }
    }

    /**
     * Merge resources.
     *
     * @param \Selene\Module\DI\BuilderInterface $builder
     *
     * @return void
     */
    protected function mergeResources(BuilderInterface $builder)
    {
        $this->resources->setResources(array_unique(
            array_merge($this->getResources(), $builder->getResources())
        ));
    }
}
