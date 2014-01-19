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

use \Selene\Components\DI\Exception\ContainerLockedException;
use \Selene\Components\DI\Exception\ContainerResolveException;

/**
 * @class Container implements ContainerInterface, InspectableInterface
 *
 * @see ContainerInterface
 * @see InspectableInterface
 *
 * @package Selene\Components\DI
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
     * resolve
     *
     * @var mixed
     */
    protected $resolve;

    /**
     * paramDelimitter
     *
     * @var string
     */
    protected static $paramDelimitter = '%';

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
        $this->resolve = false;
        $this->aliases = new Aliases;
    }

    /**
     * inspect
     *
     * @param InspectorInterface $inspector
     *
     * @access public
     * @return mixed
     */
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

        throw new ContainerResolveException(sprintf('service `%s` not found', $service));
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
    public function injectService($service, $instance, $scope = ContainerInterface::SCOPE_CONTAINER)
    {
        $this->services[$service] = $this->getDefinition(get_class($instance), null, $scope);
        $this->services[$service]->setResolved($instance);
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
    public function getParam($param)
    {
        return $this->parameters->get($param);
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
    public function setService($service, $class = null, $arguments = null, $scope = ContainerInterface::SCOPE_CONTAINER)
    {
        if (isset($this->parameters[$class])) {
            $class = $this->parameters[$class];
        }

        $this->services[$service] =
            $definition = $this->getDefinition($class, $arguments, $scope);

        return $definition;
    }

    /**
     * alias for set service.
     *
     * @param mixed $service
     * @param mixed $class
     * @param mixed $arguments
     * @param mixed $scope
     *
     * @access public
     * @return mixed
     */
    public function set($service, $class = null, $arguments = null, $scope = ContainerInterface::SCOPE_CONTAINER)
    {
        return $this->setService($service, $class, $arguments, $scope);
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
     * isReference
     *
     * @param string $reference
     *
     * @access public
     * @return boolean
     */
    public function isReference($reference)
    {
        return $reference instanceof Reference ||
            (is_string($reference) &&
            strStartsWith(static::SERVICE_REF_INDICATOR, $reference) && $this->hasService(substr($reference, 1))
        );
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

        if (isset($this->resolve[$service])) {
            throw new \RuntimeException(
                sprintf('Circular reference on %s while resolving', $service)
            );
        }

        if ($this->hasMethod($service)) {
            $instance = $this->$method();
        } else {
            $this->resolve[$service] = true;

            $instance;
            $definition->setClass($class = $this->parameters->resolveParam($definition->getClass()));
            $params = $this->resolveServiceArgs($definition);

            if ($definition->hasFactory()) {
                $instance = $this->getInstanceFromFactory($definition);
            } else {


                if (count($params) > 0) {
                    $instance = (new \ReflectionClass($class))->newInstanceArgs($params);
                } else {
                    $instance = new $class;
                }
            }

            $this->postProcessInstance($definition, $instance);

            if ($definition->scopeIsContainer()) {
                $definition->setResolved($instance);
            }
        }

        unset($this->resolve[$service]);
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

        $args = $this->resolveServiceArgs($definition);
        array_unshift($args, $definition->getClass());
        return call_user_func_array($class.'::'.$method, $args);
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
            if (($parameters = $this->parameters->get($parentClass)) && is_array($parameters)) {
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

            foreach ($this->parameters->resolveParam($args) as $argument) {

                if ($this->isReference($argument)) {
                    $arguments[] = $this->getService($this->getNameFromReference($argument));
                    continue;
                }

                $arguments[] = $argument;
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
    protected function getDefinition($class = null, $arguments = null, $scope = null)
    {
        return new Definition($class, $arguments, $scope);
    }

    /**
     * hasMethod
     *
     * @param mixed $service
     * @param string $prefix
     *
     * @access protected
     * @return mixed
     */
    protected function hasMethod($service, $prefix = 'get_')
    {
        return method_exists($this, static::camelCaseStr($prefix.$service));
    }

    /**
     * camelCaseStr
     *
     * @param mixed $str
     *
     * @access public
     * @return string
     */
    public static function camelCaseStr($str)
    {
        return strCamelCase('get.'.$str, ['_' => ' ', '.' => '_ ', '\\' => '_ ']);
    }
}
