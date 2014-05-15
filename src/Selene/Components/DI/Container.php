<?php

/**
 * This File is part of the Selene\Components\DI package.
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\DI;

use \DomainException;
use \BadMethodCallException;
use \InvalidArgumentException;
use \Selene\Components\Common\Data\BaseList;
use \Selene\Components\Common\Data\ListInterface;
use \Selene\Components\Common\Helper\StringHelper;
use \Selene\Components\DI\Definition\ClassDefinition;
use \Selene\Components\DI\Definition\ServiceDefinition;
use \Selene\Components\DI\Definition\DefinitionInterface;
use \Selene\Components\DI\Exception\ContainerResolveException;
use \Selene\Components\DI\Exception\CircularReferenceException;
use \Selene\Components\DI\Resolve\ResolveStrategyCollection;
use \Selene\Components\DI\Processor\Processor;
use \Selene\Components\DI\Processor\ProcessArgumentReference;
use \Selene\Components\DI\Processor\ResolveDefinition;
use \Selene\Components\DI\Processor\ResolveFactoryClosures;
use \Selene\Components\DI\Processor\ResolveCircularReference;
use \Selene\Components\DI\Processor\ResolveDefinitionFactoryArgs;
use \Selene\Components\Config\Resource\FileResource;
use \Selene\Components\Config\Resource\ObjectResource;

/**
 * @class BaseContainer implements ContainerInterface
 * @see ContainerInterface
 *
 * @package Selene\Components\DI
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class Container implements ContainerInterface
{
    /**
     * parameters
     *
     * @var \Selene\Components\DI\ParameterInterface
     */
    protected $parameters;

    /**
     * aliases
     *
     * @var \Selene\Components\DI\Aliases
     */
    protected $aliases;

    /**
     * definitions
     *
     * @var array
     */
    protected $definitions;

    /**
     * services
     *
     * @var array
     */
    protected $services;

    /**
     * classes
     *
     * @var array
     */
    protected $classes;

    /**
     * instances
     *
     * @var array
     */
    protected $instances;

    /**
     * injected
     *
     * @var array
     */
    protected $injected;

    /**
     * building
     *
     * @var array
     */
    protected $building;

    /**
     * synced
     *
     * @var array
     */
    protected $synced;

    /**
     * Create a new Container.
     *
     * @param ParameterInterace $parameters
     * @param mixed $name
     *
     * @access public
     */
    public function __construct(ParameterInterface $parameters = null)
    {
        //$this->resources = $resources ?: new BaseList;
        $this->parameters = $parameters ?: new Parameters;
        $this->synced = [];
        $this->services = [];
        $this->definitions = [];
        $this->injected = [];
        $this->building = [];
        //$this->packageConfigs = [];
        $this->setAliases();

    }

    /**
     * set a parameter on the parameters collection.
     *
     * @param string $parameter  the parameter key
     * @param mixed $value the parameter value
     *
     * @access public
     * @return void
     */
    public function setParameter($parameter, $value)
    {
        $this->parameters->set($parameter, $value);
    }

    /**
     * Get a parameter by its key
     *
     * @param string $parameter
     *
     * @access public
     * @return mixed the parameter value.
     */
    public function getParameter($parameter)
    {
        return $this->parameters->get($parameter);
    }

    /**
     * Check if a parameter exists.
     *
     * @param string $parameter
     *
     * @access public
     * @return boolean
     */
    public function hasParameter($parameter)
    {
        return $this->parameters->has($parameter);
    }

    /**
     * Get the parameter collection of the service.
     *
     * @access public
     * @return \Selene\Components\DI\Parameters the parameter collection object.
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * replaceParameters
     *
     * @param ParameterInterface $parameters
     *
     * @access public
     * @return mixed
     */
    public function replaceParameters(ParameterInterface $parameters)
    {
        if ($this->isLocked()) {
            throw new \BadMethodCallException();
        }
        $this->parameters = $parameters;
    }

    /**
     * getFlaggedDefinitions
     *
     * @access public
     * @return mixed
     */
    public function getFlaggedDefinitions()
    {
        return array_filter($this->getDefinitions(), function ($def) {
            return (bool)$def->getFlags();
        });
    }

    /**
     * define
     *
     * @param string $id
     * @param string $class
     * @param array  $arguments
     * @param mixed  $scope
     *
     * @access public
     * @return DefinitionInterface returns an instance of ServiceDefinition.
     */
    public function define($id, $class = null, array $arguments = [], $scope = self::SCOPE_CONTAINER)
    {
        return $this->setDefinition($id, new ServiceDefinition($class, $arguments, $scope));
    }

    /**
     * setDefinition
     *
     * @param string $id
     * @param DefinitionInterface $service
     *
     * @access public
     * @return DefinitionInterface returns an instance of ServiceDefinition.
     */
    public function setDefinition($id, DefinitionInterface $service)
    {
        $id = strtolower($id);
        return $this->definitions[$id] = $service;
    }

    /**
     * Injects a service into the container.
     *
     * @param string $id the service id.
     * @param Object $instance the service instance.
     * @param string $scope the service scope.
     *
     * @throws InvalidArgumentException when the scope contains `prototype`.
     * @throws DomainException
     * @access public
     * @return void
     */
    public function inject($id, $instance, $scope = self::SCOPE_CONTAINER)
    {
        if (static::inScopes(static::SCOPE_PROTOTYPE, $scope)) {
            throw new InvalidArgumentException(sprintf('An injected service must not have a prototype scope'));
        }

        $id = $this->resolveId($id);
        $defined = $this->hasDefinition($id);

        if (!$this->isLocked()) {
            if (!$defined) {
                $this->define($id)
                    ->setInjected(true);
            } else {
                $this->getDefinition($id)->setInjected(true);
            }
        }

        $this->injected[$id] = true;
        $this->services[$id] = $instance;

        $this->sync($id);
    }

    /**
     * Retrieve a service by its id.
     *
     * @param string $id the service id.
     *
     * @access public
     * @throws ContainerResolveException if the service is not resolveable.
     * @throws BadMethodCallException if a setter doesn't exist on a service.
     * @return mixed
     */
    public function get($id)
    {

        if (method_exists($this, $method = sprintf('get%sService', static::camelCaseStr($id)))) {
            return $this->{$method}($id);
        }

        // treat internal services as undefiend.
        if (!$this->hasDefinition($id) || (empty($this->building) && $this->getDefinition($id)->isInternal())) {

            throw new ContainerResolveException(sprintf('A service with id %s was is not defined', $id));
        }

        if (isset($this->services[$id = $this->resolveId($id)])) {
            return $this->services[$id];
        }

        if ($this->getDefinition($id)->isAbstract()) {
            throw new ContainerResolveException(
                sprintf('Service %s is declared abstract. Instantiating abstract services is not allowed.', $id)
            );
        }

        $instance = $this->buildService($id);

        return $this->getDefinition($id)->scopeIsContainer() ? $this->services[$id] = $instance : $instance;
    }

    /**
     * Check if a service has been defined.
     *
     * @param string $id the service id.
     *
     * @access public
     * @return boolean
     */
    public function hasDefinition($id)
    {
        $id = $this->resolveId($id);
        return isset($this->definitions[$id]) || array_key_exists($id, $this->definitions);
    }

    /**
     * Get a service definition by id.
     *
     * @param string $id the service id.
     *
     * @access public
     * @return ServiceDefinition
     */
    public function getDefinition($id)
    {
        return $this->definitions[$this->resolveId($id)];
    }

    /**
     * Get all service definitions.
     *
     * @access public
     * @return array
     */
    public function getDefinitions()
    {
        return $this->definitions;
    }

    /**
     * Like `Container::hasDefinition()` but also checks for `$this->serivces`.
     *
     * @param string $id the service id.
     *
     * @access public
     * @return boolean
     */
    public function hasService($id)
    {
        $id = $this->resolveId($id);
        return isset($this->services[$id]) || array_key_exists($id, $this->definitions);
    }

    /**
     * Alias a service.
     *
     * @param string $alias the alias
     * @param string $id the service id
     *
     * @access public
     * @throws InvalidArgumentException if a service with given alias alread
     * exists.
     * @throws InvalidArgumentException if the alias is the same as the id.
     * @return void
     */
    public function setAlias($alias, $id)
    {
        if (isset($this->definitions[$alias = strtolower($alias)])) {
            throw new InvalidArgumentException(sprintf('A service with id %s is already defined', $alias));
        }

        if (0 === strcasecmp($alias, $id)) {
            throw new InvalidArgumentException(sprintf('Alias \'%s\' and id \'%s\' can\'t be the same.', $alias, $id));
        }

        $this->aliases->set($alias, strtolower($id));
    }

    /**
     * setAliases
     *
     * @param Aliases $aliases
     *
     * @access public
     * @return void
     */
    public function setAliases(Aliases $aliases = null)
    {
        $this->aliases = $aliases ?: new Aliases;
    }

    /**
     * getAlias
     *
     * @param string $alias
     *
     * @access public
     * @return string
     */
    public function getAlias($alias)
    {
        return $this->aliases->get($alias);
    }

    /**
     * removeAlias
     *
     * @param mixed $alias
     *
     * @access public
     * @return mixed
     */
    public function removeAlias($alias)
    {
        unset($this->Aliases[$alias]);
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
            (
                0 === strpos($reference, static::SERVICE_REF_INDICATOR)
                //0 === strpos($reference, static::SERVICE_REF_INDICATOR) &&
                //$this->hasDefinition($this->getReferenceId($reference))
            )
        );
    }

    /**
     * isLocked
     *
     * @access public
     * @return boolean
     */
    public function isLocked()
    {
        return $this->parameters instanceof StaticParameters;
    }

    /**
     * merge
     *
     * @param ContainerInterface $container
     *
     * @throws BadMethodCallException
     * @access public
     * @return void
     */
    public function merge(ContainerInterface $container)
    {
        if ($this->isLocked() || $container->isLocked()) {
            throw new BadMethodCallException('cannot merge a locked container');
        }

        $this->parameters->merge($container->getParameters());
        $this->definitions = array_merge((array)$this->definitions, (array)$container->getDefinitions());

        $this->services = array_merge($container->getServices(), $this->services);
    }

    /**
     * getServices
     *
     * @access public
     * @return array
     */
    public function getServices()
    {
        return $this->services;
    }

    /**
     * resolveId
     *
     * @param mixed $id
     *
     * @access protected
     * @return string
     */
    protected function resolveId($id)
    {
        return (string)$this->aliases->get(strtolower($id));
    }

    /**
     * inScopes
     *
     * @param string $needle the scope to check against other scopes.
     * @param string $heystack a given scope or a scope range.
     *
     * @access protected
     * @return boolean
     */
    public static function inScopes($needle, $heystack)
    {
        return $needle === ($needle & $heystack);
    }

    /**
     * camelCaseStr
     *
     * @param string $str
     *
     * @access public
     * @return string
     */
    public static function camelCaseStr($str)
    {
        return StringHelper::strCamelCase($str, ['_' => ' ', '.' => 'Nss ', '\\' => 'Dbs ']);
    }

    /**
     * buildService
     *
     * @param string $id
     *
     * @throws \Selene\Components\DI\Exception\CircularReferenceException
     * @throws RuntimeException
     * @access protected
     * @return object the service instance.
     */
    protected function buildService($id)
    {
        if (isset($this->building[$id])) {
            throw new CircularReferenceException(
                sprintf('service %s is in a circular reference', $id)
            );
        }

        $this->building[$id] = true;

        $definition = $this->getDefinition($id);

        if ($definition->requiresFile()) {
            require_once $this->parameters->resolveParam($definition->getFile());
        }

        if (!class_exists($class = $this->parameters->resolveParam($definition->getClass()))) {
            throw new \RuntimeException(sprintf('Class %s does not exist', $class));
        }

        $parent = $definition->hasParent() ?
            $this->getDefinition($this->parameters->resolveParam($definition->getParent())) :
            null;

        $definition->setClass($class);
        $parent && $definition->setParent($parent);

        if ($definition->hasFactory()) {
            $instance = $this->buildFromFactory($definition, $parent);
        } else {
            $instance = $this->buildFromDefinition($definition, $parent);
        }

        if (((bool)$parent && $parent->hasSetters()) || $definition->hasSetters()) {
            $this->callSetters($instance, $parent ? $parent->getSetters() : $definition->getSetters());
        }

        unset($this->building[$id]);
        return $instance;
    }

    /**
     * buildFromFactory
     *
     * @param DefinitionInterface $definition
     * @param DefinitionInterface $parent
     *
     * @access protected
     * @return mixed
     */
    protected function buildFromFactory(DefinitionInterface $definition, DefinitionInterface $parent = null)
    {
        return $this->callFactory(
            $definition->getClass(),
            $definition->getFactory(),
            $parent ? $parent->getArguments() : $definition->getArguments()
        );
    }

    /**
     * buildFromDefinition
     *
     * @param DefinitionInterface $definition
     * @param DefinitionInterface $parent
     *
     * @access protected
     * @return Object returns a class instance.
     */
    protected function buildFromDefinition(DefinitionInterface $definition, DefinitionInterface $parent = null)
    {
        $reflection = new \ReflectionClass($class = $definition->getClass());

        if ($reflectionConstructor = $reflection->getConstructor()) {
            $arguments = $parent ? $parent->getArguments() :  $definition->getArguments();
            $instance = $reflection->newInstanceArgs($this->getServiceArguments($arguments));
        } else {
            $instance = new $class;
        }

        return $instance;
    }

    /**
     * getServiceArguments
     *
     * @param array $arguments
     *
     * @access protected
     * @return array
     */
    protected function getServiceArguments(array $arguments)
    {
        foreach ($arguments as $i => $argument) {

            if ($this->isReference($argument)) {

                $arguments[$i] = $this->get($this->getReferenceId($argument));
                continue;
            }

            if (is_array($argument)) {
                $arguments[$i] = $this->getServiceArguments($argument);
                continue;
            }

            $arguments[$i] = $this->parameters->resolveParam($argument);
        }
        return $arguments;
    }

    /**
     * callSetters
     *
     * @param mixed $instance
     * @param array $callers
     *
     * @throws BadMethodCallException
     * @access protected
     * @return void
     */
    protected function callSetters($instance, array $callers)
    {
        foreach ($callers as $method => $argsList) {

            //$method  = key($caller);
            foreach ($argsList as $args) {
                $synced  = $this->getSyncedArguments($args);

                if (!empty($synced)) {
                    return $this->createSyncCallback($instance, $method, $args, $synced);
                }

                $this->applySetter($instance, $method, $args);
            }
            //$args    = $caller[$method];

        }
    }

    /**
     * applySetter
     *
     * @param mixed $instance
     * @param mixed $method
     * @param mixed $arguments
     *
     * @access protected
     * @return void
     */
    protected function applySetter($instance, $method, $arguments)
    {
        if (!method_exists($instance, $method)) {
            throw new BadMethodCallException(
                sprintf('method %s::%s() does not exist', get_class($instance), $method)
            );
        }

        $arguments = $this->getServiceArguments($arguments);

        call_user_func_array([$instance, $method], $arguments);
    }

    /**
     * callFactory
     *
     * @param string $class
     * @param string|array $factory
     * @param array $arguments
     *
     * @throws InvalidArgumentException if $factory is unresolvable
     * @access protected
     * @return mixed|object an instance of the class defined by a factory.
     */
    protected function callFactory($class, $factory, array $arguments)
    {
        $arguments = $this->getServiceArguments($arguments);
        array_unshift($arguments, $class);

        if (is_callable($factory = $this->parameters->resolveParam($factory))) {
            return call_user_func_array($factory, $arguments);
        }

        throw new InvalidArgumentException('Factory is not callable');
    }

    /**
     * getReferenceId
     *
     * @param string|object $reference a string or an instance of Reference
     *
     * @access protected
     * @return string
     */
    protected function getReferenceId($reference)
    {
        return 0 === strpos($reference, static::SERVICE_REF_INDICATOR) ?
            substr($reference, strlen(static::SERVICE_REF_INDICATOR)) :
            (string)$reference;
    }

    /**
     * sync
     *
     * @param mixed $id
     *
     * @access protected
     * @return mixed
     */
    protected function sync($id)
    {
        if (isset($this->synced[$id])) {

            foreach ($this->synced[$id] as $callback) {
                call_user_func($callback, $id);
            }
        }
    }

    /**
     * getSyncedArguments
     *
     * @param mixed $arguments
     *
     * @access protected
     * @return mixed
     */
    protected function getSyncedArguments($arguments)
    {
        $args = [];

        foreach ($arguments as $argument) {
            if ($this->isReference($argument) &&
                ($def = $this->getDefinition($id = $this->getReferenceId($argument))) &&
                $def->isInjected() && !isset($this->services[$id])
            ) {
                $args[$id] = true;
            }
        }

        return $args;
    }

    /**
     * addSnycedCaller
     *
     * @param mixed $instance
     * @param mixed $method
     * @param array $arguments
     * @param array $synced
     *
     * @access protected
     * @return void
     */
    protected function createSyncCallback($instance, $method, array $arguments = [], array $synced = [])
    {
        $callback = function ($id) use ($instance, $method, $arguments, &$synced) {
            unset($synced[$id]);

            if (empty($synced)) {
                return $this->applySetter($instance, $method, $arguments);
            }

        };

        $this->pushSyncedCallers($synced, $callback);
    }

    /**
     * pushSyncedCallers
     *
     * @param array $synced
     * @param Closure $callback
     *
     * @access protected
     * @return void
     */
    protected function pushSyncedCallers(array $synced, \Closure $callback)
    {
        foreach ($synced as $serviceId => $sync) {
            $this->synced[$serviceId][] =& $callback;
        }
    }
}
