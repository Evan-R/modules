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
use \Selene\Components\Common\Data\BaseList;
use \Selene\Components\Common\Data\ListInterface;
use \Selene\Components\DI\Dumper\ContainerDumper;
use \Selene\Components\DI\Processor\Processor;
use \Selene\Components\DI\Processor\ProcessorInterface;
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
     * @var mixed
     */
    protected $processor;

    /**
     * container
     *
     * @var mixed
     */
    protected $container;

    /**
     * extensions
     *
     * @var ListInterface
     */
    protected $resources;

    /**
     * container
     *
     * @var array
     */
    protected $extensions;

    /**
     * __construct
     *
     * @param Dumper $dumper
     *
     * @access public
     * @return mixed
     */
    public function __construct(
        ContainerInterface $container,
        ProcessorInterface $processor = null,
        ListInterface $resources = null
    ) {
        $this->container = $container;
        $this->processor = $processor ?: new Processor;
        $this->resources = $resources ?: new BaseList;

        $this->extensions = [];
    }

    /**
     * getContainer
     *
     * @access public
     * @return mixed
     */
    public function getContainer()
    {
        return $this->container;
    }

    public function replaceContainer(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * merge
     *
     * @param BuilderInterface $builder
     *
     * @access public
     * @return mixed
     */
    public function merge(BuilderInterface $builder)
    {
        $this->mergeExtensionConfigs($builder);
        $this->mergeResources($builder);

        $this->container->merge($builder->getContainer());
    }

    /**
     * getProcessor
     *
     * @access public
     * @return mixed
     */
    public function getProcessor()
    {
        return $this->processor;
    }

    /**
     * build
     *
     *
     * @access public
     * @return mixed
     */
    public function build()
    {
        $this->container->getParameters()->resolve()->all();
        //Parameters->replaceParameters($parameters);
        $this->processor->process($this->container);
    }

    /**
     * addFileResource
     *
     * @param mixed $file
     *
     * @access public
     * @return void
     */
    public function addFileResource($file)
    {
        $this->resources->add(new FileResource($file));
    }

    /**
     * addObjectResource
     *
     * @param mixed $object
     *
     * @access public
     * @return void
     */
    public function addObjectResource($object)
    {
        $this->resources->add(new ObjectResource($object));
    }

    /**
     * getResources
     *
     *
     * @access public
     * @return mixed
     */
    public function getResources()
    {
        return $this->resources;
    }

    /**
     * addExtensionConfig
     *
     * @param mixed $extension
     * @param array $config
     *
     * @access public
     * @return mixed
     */
    public function addExtensionConfig($extension, array $config)
    {
        $this->extensions[$extension][] = $config;
    }

    /**
     * getExtensionConfig
     *
     * @param mixed $extension
     *
     * @access public
     * @return array
     */
    public function getExtensionConfig($extension)
    {
        return $this->getDefault($this->extensions, $extension, []);
    }

    /**
     * getExtensionConfigs
     *
     * @param mixed $extension
     *
     * @access public
     * @return array
     */
    public function getExtensionConfigs()
    {
        return $this->extensions;
    }

    /**
     * __call
     *
     * @param mixed $method
     * @param mixed $arguments
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
     * mergeExtensionConfigs
     *
     * @param mixed $builder
     *
     * @access protected
     * @return mixed
     */
    protected function mergeExtensionConfigs($builder)
    {
        foreach ($builder->getExtensionConfigs() as $extension => $config) {
            $this->extensions[$extension] = array_merge($this->getExtensionConfig($extension), $config);
        }
    }

    /**
     * mergeResources
     *
     * @param mixed $builder
     *
     * @access protected
     * @return mixed
     */
    protected function mergeResources($builder)
    {
        $this->resources = new BaseList(array_unique(
            array_merge($this->resources->toArray(), $builder->getResources()->toArray())
        ));
    }
}
