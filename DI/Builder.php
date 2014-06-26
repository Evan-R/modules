<?php

/**
 * This File is part of the Selene\Components\DI package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\DI;

use \Selene\Components\Common\Traits\Getter;
use \Selene\Components\DI\Dumper\ContainerDumper;
use \Selene\Components\DI\Processor\Processor;
use \Selene\Components\DI\Processor\ProcessorInterface;
use \Selene\Components\DI\Processor\ResolveDefinitionFactoryArgsPass;
use \Selene\Components\DI\Processor\ResolveDefinitionArguments;
use \Selene\Components\DI\Processor\ResolveDefinitionDependencies;
use \Selene\Components\DI\Processor\ResolveParentDefinition;
use \Selene\Components\DI\Processor\ResolveCircularReference;
use \Selene\Components\DI\Processor\RemoveAbstractDefinition;
use \Selene\Components\DI\Processor\ResolveCallerMethodCalls;
use \Selene\Components\Config\Resource\FileResource;
use \Selene\Components\Config\Resource\ObjectResource;

/**
 * @class Builder implements BuilderInterface
 * @see BuilderInterface
 *
 * @package Selene\Components\DI
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
     * @var \Selene\Components\DI\Processor\ProcessorInterface
     */
    protected $processor;

    /**
     * container
     *
     * @var \Selene\Components\DI\ContainerInterface
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
    protected $extensions;

    protected $configured;

    /**
     * Create a new Builder instance.
     *
     * @param Dumper $dumper
     *
     * @access public
     */
    public function __construct(
        ContainerInterface $container,
        ProcessorInterface $processor = null,
        array $resources = []
    ) {
        $this->container = $container;
        $this->processor = $processor ?: new Processor;
        $this->resources = $resources;

        $this->extensions = [];
    }

    /**
     * Get the current DI Container
     *
     * @access public
     * @return \Selene\Components\DI\ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Replaces the current DI Container.
     *
     * @param \Selene\Components\DI\ContainerInterface $container a new
     * container.
     *
     * @access public
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
     * @param \Selene\Components\DI\BuilderInterface $builder
     *
     * @access public
     * @return voi
     */
    public function merge(BuilderInterface $builder)
    {
        $this->mergeExtensionConfigs($builder);
        $this->mergeResources($builder);

        $this->container->merge($builder->getContainer());
    }

    /**
     * Get the current Container Processor.
     *
     * @access public
     * @return \Selene\Components\DI\Processor\ProcessorInterface
     */
    public function getProcessor()
    {
        return $this->processor;
    }

    /**
     * Starts Building the DI Container.
     *
     * @access public
     * @return void
     */
    public function build()
    {
        $this->configure();

        $this->container->getParameters()->resolve()->all();
        $this->processor->process($this->container);
    }

    /**
     * configure
     *
     * @access public
     * @return boolean
     */
    public function configure()
    {
        if ($this->configured) {
            return false;
        }

        return $this->configureProcessor($this->processor);
    }

    /**
     * configureProcessor
     *
     * @param ProcessorInterface $processor
     *
     * @access protected
     * @return boolean
     */
    protected function configureProcessor(ProcessorInterface $processor)
    {
        $processor->add(new ResolveParentDefinition, ProcessorInterface::OPTIMIZE);
        $processor->add(new ResolveDefinitionDependencies, ProcessorInterface::OPTIMIZE);
        $processor->add(new ResolveDefinitionArguments, ProcessorInterface::OPTIMIZE);
        $processor->add(new ResolveCircularReference, ProcessorInterface::OPTIMIZE);
        $processor->add(new RemoveAbstractDefinition, ProcessorInterface::REMOVE);
        $processor->add(new ResolveCallerMethodCalls, ProcessorInterface::AFTER_REMOVE);

        return $this->configured = true;
    }

    /**
     * Add a file resource to track.
     *
     * @param string $file Path to a file
     *
     * @access public
     * @return void
     */
    public function addFileResource($file)
    {
        $this->resources[] = new FileResource($file);
    }

    /**
     * Add an object resource to track.
     *
     * @param object $object any serilizable objet.
     *
     * @access public
     * @return void
     */
    public function addObjectResource($object)
    {
        $this->resources[] = new ObjectResource($object);
    }

    /**
     * Dump all tracked resources.
     *
     * @access public
     * @return array
     */
    public function getResources()
    {
        return $this->resources;
    }

    /**
     * Add a extension configuration array.
     *
     * @param string $extension the extension id
     * @param array $config the config array
     *
     * @access public
     * @return void
     */
    public function addExtensionConfig($extension, array $config)
    {
        $this->extensions[$extension][] = $config;
    }

    public function addPackageConfig($package, array $config)
    {

    }

    /**
     * Get all config arrays for a given extension id.
     *
     * @param string $extension the extension id.
     *
     * @access public
     * @return array
     */
    public function getExtensionConfig($extension)
    {
        return $this->getDefault($this->extensions, $extension, []);
    }


    /**
     * Get all extension config arrays.
     *
     * @access public
     * @return array
     */
    public function getExtensionConfigs()
    {
        return $this->extensions;
    }

    /**
     * Forward member calls to the container.
     *
     * @param string $method
     * @param array $arguments
     *
     * @throws \BadMethodCallException if the method does not exist.
     *
     * @access public
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        if (method_exists($this->container, $method)) {
            return call_user_func_array([$this->container, $method], $arguments);
        }

        throw new \BadMethodCallException(sprintf('call to undefined method %s::%s()', get_class($this), $method));
    }

    /**
     * Merge extension configs.
     *
     * @param \Selene\Components\DI\BuilderInterface $builder
     *
     * @access protected
     * @return void
     */
    protected function mergeExtensionConfigs(BuilderInterface $builder)
    {
        foreach ($builder->getExtensionConfigs() as $extension => $config) {
            $this->extensions[$extension] = array_merge($this->getExtensionConfig($extension), $config);
        }
    }

    /**
     * Merge resources.
     *
     * @param \Selene\Components\DI\BuilderInterface $builder
     *
     * @access protected
     * @return void
     */
    protected function mergeResources(BuilderInterface $builder)
    {
        $this->resources = array_unique(
            array_merge($this->resources, $builder->getResources())
        );
    }
}
