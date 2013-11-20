<?php

/**
 * This File is part of the Selene\Components\DependencyInjection package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\DependencyInjection;

use \Selene\Components\DependencyInjection\Exception\ContainerLockedException;

/**
 * @class Container implements ContainerInterface, InspectableInterface
 *
 * @see ContainerInterface
 * @see InspectableInterface
 *
 * @package Selene\Components\DependencyInjection
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class Container implements ContainerInterface, InspectableInterface
{
    /**
     * parameters
     *
     * @var Parameters
     */
    protected $parameters;

    /**
     * aliases
     *
     * @var mixed
     */
    protected $aliases;

    /**
     * services
     *
     * @var mixed
     */
    protected $services;

    /**
     * locked
     *
     * @var boolean
     */
    protected $locked;

    /**
     * name
     *
     * @var string
     */
    protected $name;

    /**
     *
     * @param Parameters $parameters
     *
     * @access public
     */
    public function __construct(Parameters $parameters = null, $name = self::APP_CONTAINER_SERVICE)
    {
        $this->name = $name;
        $this->locked = false;
        $this->setParameters($parameters);
        $this->injectService($this->name, $this);
        $this->aliases = new Aliases;
    }

    public function inspect()
    {
        return null;
    }

    /**
     * setParameter
     *
     * @param Parameters $parameters
     *
     * @access protected
     * @return void
     */
    public function setParameters(Parameters $parameters = null)
    {
        if (null === $parameters) {
            $parameters = new Parameters;
        }
        $this->parameters = $parameters;
    }

    /**
     * getParameters
     *
     * @param mixed $param
     *
     * @access public
     * @return ParameterInterface
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * getName
     *
     * @access public
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * merge
     *
     * @param ContainerInterface $container
     *
     * @access public
     * @return void
     */
    public function merge(ContainerInterface $container)
    {
        if ($this->isLocked() or $container->isLocked()) {
            throw new ContainerLockedException('cannot merge a locked container');
        }

        if ($this->getName() === $container->getName()) {
            throw new \LogicException(sprintf('cannot merge containers sharing the same name %s', $this->getName()));
        }

        $this->parameters->merge($container->getParameters());
        $this->services = array_merge((array)$this->services, (array)$container->getServices());
    }

    /**
     * getServices
     *
     * @access public
     * @return mixed
     */
    public function getServices()
    {
        return $this->services;
    }

    /**
     * isLocked
     *
     * @access public
     * @return mixed
     */
    public function isLocked()
    {
        return $this->locked;
    }

    /**
     * setParam
     *
     * @param mixed $param
     * @param mixed $definition
     *
     * @access public
     * @return mixed
     */
    public function setParam($param, $definition)
    {
        $this->parameters->set($param, $definition);
    }

    /**
     * getParam
     *
     * @param mixed $parameter
     * @param mixed $definition
     *
     * @access public
     * @return mixed
     */
    public function getParam($parameter)
    {
        return $this->parameters->get($parameter);
    }

    /**
     * setService
     *
     * @param string $service
     * @param string $class
     *
     * @access public
     * @return Definition
     */
    public function setService($service, $class = null, $arguments = null)
    {
        $this->services[$this->parameters->get($service)] =
            $definition = $this->getDefinition($this->parameters->get($class), $arguments);

        return $definition;
    }

    /**
     * alias
     *
     * @param mixed $service
     * @param mixed $alias
     *
     * @access public
     * @return mixed
     */
    public function alias($service, $alias)
    {
        $this->aliases->add($alias, $service);
    }

    /**
     * getService
     *
     * @param mixed $service
     *
     * @access public
     * @return mixed
     */
    public function getService($service)
    {
        $service = $this->aliases->get($service);

        if ($this->hasService($service)) {
            return $this->resolveService($service);
        }

        throw new \Exception(sprintf('service %s not found', $service));
    }

    /**
     * hasService
     *
     * @param string $service
     *
     * @access public
     * @return boolean
     */
    public function hasService($service)
    {
        return is_string($service) && isset($this->services[$service]);
    }

    /**
     * injectService
     *
     * @param string $service
     * @param object $instance
     *
     * @access public
     * @return voic
     */
    public function injectService($service, $instance)
    {
        $this->services[$service] = $this->getDefinition(get_class($instance));
        $this->services[$service]->setResolved($instance);
    }

    /**
     * isReference
     *
     * @param string $reference
     *
     * @access public
     * @return boolean
     */
    public function isReference($reference)
    {
        return 0 === strrpos($reference, static::SERVICE_REF_INDICATOR) && $this->hasService(substr($reference, 1));
    }

    /**
     * resolveService
     *
     * @param string $service
     *
     * @access protected
     * @return Object the service instance
     */
    protected function resolveService($service)
    {
        $definition = $this->services[$service];

        if ($definition->isResolved()) {
            return $definition->getResolved();
        }

        $params = $this->resolveServiceArgs($definition);
        $instance;

        if ($definition->hasFactory()) {
            $instance = $this->getInstanceFromFactory($definition);
        } else {

            if (count($params) > 0) {
                $instance = $this->setClassArgs($definition->getClass(), $params);
            } else {
                $class = $definition->getClass();
                $instance = new $class;
            }
        }

        $this->postProcessInstance($definition, $instance);

        if ($definition->scopeIsContainer()) {
            $definition->setResolved($instance);
        }

        return $instance;
    }

    /**
     * postProcessInstance
     *
     * @param Definition $definition
     * @param Object $instance
     *
     * @access protected
     * @return void
     */
    protected function postProcessInstance(Definition $definition, $instance)
    {
        if ($definition->hasSetters()) {
            foreach ($definition->getSetters() as $setter) {
                call_user_func_array([$instance, $setter['method']], $this->resolveArgs($setter['arguments']));
            }
        }
    }

    /**
     * getInstanceFromFactory
     *
     * @param Definition $definition
     *
     * @access protected
     * @return Object
     */
    protected function getInstanceFromFactory(Definition $definition)
    {
        extract($definition->getFactory());
        return call_user_func_array($class.'::'.$method, $this->resolveServiceArgs($definition));
    }

    /**
     * getDefinitionArguments
     *
     * @param Definition $definition
     *
     * @access protected
     * @return mixed
     */
    protected function getDefinitionArguments(Definition $definition)
    {
        $args = $definition->getArguments();

        if ($parentClass = $definition->getParent()) {
            if ($parameters = $this->parameters->get('@'.$parentClass)) {
                $args = array_unique(array_merge($parameters, $args));
            }
        }

        return $args;

    }

    /**
     * resolveServiceArgs
     *
     * @param Definition $definition
     *
     * @access protected
     * @return array
     */
    protected function resolveServiceArgs(Definition $definition)
    {
        return $this->resolveArgs($this->getDefinitionArguments($definition));
    }

    /**
     * resolveArgs
     *
     * @param array $args
     *
     * @access protected
     * @return array
     */
    protected function resolveArgs(array $args)
    {
        $arguments = [];

        if (!empty($args)) {
            foreach ($args as $argument) {

                if (!is_string($argument)) {
                    $arguments[] = $argument;
                    continue;
                }

                if ($this->isReference($argument)) {
                    $arguments[] = $this->getService($this->getNameFromReference($argument));
                    continue;
                }

                $arguments[] = $this->parameters->get($argument);
            }
        }
        return $arguments;
    }

    /**
     * getNameFromReference
     *
     * @param mixed $reference
     *
     * @access protected
     * @return mixed
     */
    protected function getNameFromReference($reference)
    {
        return substr($reference, 1);
    }

    /**
     * getDefinition
     *
     * @param mixed $class
     * @param mixed $arguments
     *
     * @access protected
     * @return Definition
     */
    protected function getDefinition($class = null, $arguments = null)
    {
        return new Definition($class, $arguments);
    }

    /**
     * Create a new Class instance with its arguments
     *
     * @param string $class
     * @param array $args
     *
     * @access protected
     * @throws \InvalidArgumentException
     * @return Object
     */
    protected function setClassArgs($class, $args)
    {
        switch (count($args)) {
            case 1:
                return new $class($args[0]);
            case 2:
                return new $class($args[0], $args[1]);
            case 3:
                return new $class($args[0], $args[1], $args[2]);
            case 4:
                return new $class($args[0], $args[1], $args[2], $args[3]);
            case 5:
                return new $class($args[0], $args[1], $args[2], $args[3], $args[4]);
            case 6:
                return new $class($args[0], $args[1], $args[2], $args[3], $args[4], $args[5]);
            case 7:
                return new $class($args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6]);
            case 8:
                return new $class($args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6], $args[7]);
            default:
                throw new \InvalidArgumentException('no arguments or argument limit exceeded');
                break;
        }
    }
}
