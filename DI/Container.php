<?php

/**
 * This File is part of the Selene\Module\DI package.
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\DI;

use \BadMethodCallException;
use \InvalidArgumentException;
use \Selene\Module\Common\Helper\StringHelper;
use \Selene\Module\DI\Definition\ServiceDefinition;
use \Selene\Module\DI\Definition\DefinitionInterface;
use \Selene\Module\DI\Exception\ContainerLockedException;
use \Selene\Module\DI\Exception\ContainerResolveException;
use \Selene\Module\DI\Exception\CircularReferenceException;

/**
 * @class BaseContainer implements ContainerInterface
 * @see ContainerInterface
 *
 * @package Selene\Module\DI
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
class Container implements ContainerInterface
{
    /**
     * parameters
     *
     * @var \Selene\Module\DI\ParameterInterface
     */
    protected $parameters;

    /**
     * definitions
     *
     * @var array
     */
    protected $definitions;

    /**
     * aliases
     *
     * @var \Selene\Module\DI\Aliases
     */
    protected $aliases;

    /**
     * services
     *
     * @var array
     */
    protected $services;

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
     */
    public function __construct(ParameterInterface $parameters = null)
    {
        $this->parameters = $parameters ?: new Parameters;
        $this->setAliases();

        $this->synced      = [];
        $this->services    = [];
        $this->definitions = [];
        $this->injected    = [];
        $this->building    = [];
    }

    /**
     * isLocked
     *
     * @return boolean
     */
    public function isLocked()
    {
        return $this->parameters instanceof StaticParameters;
    }

    /**
     * set a parameter on the parameters collection.
     *
     * @param string $parameter  the parameter key
     * @param mixed $value the parameter value
     *
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
     * @return boolean
     */
    public function hasParameter($parameter)
    {
        return $this->parameters->has($parameter);
    }

    /**
     * Get the parameter collection of the service.
     *
     * @return \Selene\Module\DI\Parameters the parameter collection object.
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
     * @return void
     */
    public function replaceParameters(ParameterInterface $parameters)
    {
        if ($this->isLocked()) {
            throw ContainerLockedException::replaceParameterException();
        }

        $this->parameters = $parameters;
    }

    /**
     * getFlaggedDefinitions
     *
     * @return array
     */
    public function findDefinitionsWithMetaData($name = null)
    {
        return array_filter($this->getDefinitions(), function ($def) use ($name) {
            return (bool)$def->hasMetaData($name);
        });
    }

    /**
     * define
     *
     * @param string $id
     * @param string $class
     * @param array  $arguments
     * @param int    $scope
     *
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
     * @throws \BadMethodCallException
     *
     * @return DefinitionInterface returns an instance of ServiceDefinition.
     */
    public function setDefinition($id, DefinitionInterface $service)
    {
        if ($this->isLocked()) {
            throw ContainerLockedException::setDefinitionException($id);
        }

        return $this->definitions[$id] = $service;
    }

    /**
     * Check if a service has been defined.
     *
     * @param string $id the service id.
     *
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
     * @return ServiceDefinition
     */
    public function getDefinition($id)
    {
        return $this->definitions[$this->resolveId($id)];
    }

    /**
     * Get all service definitions.
     *
     * @return array
     */
    public function getDefinitions()
    {
        return $this->definitions;
    }

    /**
     * removeDefinition
     *
     * @param mixed $id
     *
     * @return void
     */
    public function removeDefinition($id)
    {
        unset($this->definitions[$id]);
    }

    /**
     * Injects a service into the container.
     *
     * @param string $id the service id.
     * @param Object $instance the service instance.
     * @param string $scope the service scope.
     *
     * @throws InvalidArgumentException when the scope contains `prototype`.
     *
     * @return void
     */
    public function inject($id, $instance, $scope = self::SCOPE_CONTAINER)
    {
        if (static::inScopes(static::SCOPE_PROTOTYPE, $scope)) {
            throw new InvalidArgumentException(sprintf('An injected service must not have a prototype scope'));
        }

        $id = $this->resolveId($id);

        // if the container is unlocked, define the injected service
        if (!$this->isLocked()) {
            $definition = $this->hasDefinition($id) ? $this->getDefinition($id) : $this->define($id);
            $definition->setClass(get_class($instance))->setInjected(true);
        }

        $this->services[$id] = $instance;

        $this->sync($id);
    }

    /**
     * Check if there's a definition or service.
     *
     * @param string $id
     *
     * @return boolean
     */
    public function has($id)
    {
        return $this->hasService($id) || $this->hasDefinition($id);
    }

    /**
     * Retrieve a service by its id.
     *
     * @param string $id the service id.
     *
     * @throws ContainerResolveException if the service is not resolveable.
     * @throws BadMethodCallException if a setter doesn't exist on a service.
     *
     * @return mixed
     */
    public function get($id)
    {
        if ($this->hasService($id = $this->resolveId($id))) {
            return $this->getService($id);
        }

        if (method_exists($this, $method = sprintf('getService%s', static::camelCaseStr($id)))) {
            return $this->{$method}($id);
        }

        // treat internal services as undefiend.
        if (!$this->hasDefinition($id) || (empty($this->building) && $this->getDefinition($id)->isInternal())) {
            throw new ContainerResolveException(sprintf('A service with id %s was is not defined', $id));
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
     * getServices
     *
     * @return array
     */
    public function getServices()
    {
        return $this->services;
    }

    /**
     * Checks if a service is resolved.
     *
     * @param string $id the service id.
     *
     * @return boolean
     */
    protected function hasService($id)
    {
        return isset($this->services[$id]);
    }

    /**
     * Get a service instance.
     *
     * @param mixed $id
     *
     * @return Object
     */
    protected function getService($id)
    {
        return $this->services[$id];
    }

    /**
     * Alias a service.
     *
     * @param string $alias the alias
     * @param string $id the service id
     *
     * @throws \LogicException if a service with given alias already
     * exists.
     * @throws \LogicException if the alias is the same as the id.
     *
     * @return void
     */
    public function setAlias($alias, $id)
    {
        if (isset($this->definitions[$alias = strtolower($alias)])) {
            throw new \LogicException(sprintf('A service with id %s is already defined', $alias));
        }

        if (0 === strcasecmp($alias, $id)) {
            throw new \LogicException(sprintf('Alias \'%s\' and id \'%s\' can\'t be the same.', $alias, $id));
        }

        $this->aliases->set($alias, strtolower($id));
    }

    /**
     * setAliases
     *
     * @param Aliases $aliases
     *
     * @return void
     */
    public function setAliases(Aliases $aliases = null)
    {
        $this->aliases = $aliases ?: new Aliases;
    }

    /**
     * getAliases
     *
     * @param Aliases $aliases
     *
     * @return mixed
     */
    public function getAliases()
    {
        return $this->aliases ? $this->aliases : $this->aliases = new Aliases;
    }

    /**
     * getAlias
     *
     * @param string $alias
     *
     * @return string
     */
    public function getAlias($alias)
    {
        return $this->aliases[$alias];
    }

    /**
     * removeAlias
     *
     * @param mixed $alias
     *
     * @return void
     */
    public function removeAlias($alias)
    {
        unset($this->aliases[$alias]);
    }

    /**
     * isReference
     *
     * @param string $reference
     *
     * @return boolean
     */
    public function isReference($reference)
    {
        return $reference instanceof Reference ||
            ((is_string($reference) && 0 === strpos($reference, static::SERVICE_REF_INDICATOR))
        );
    }

    /**
     * merge
     *
     * @param ContainerInterface $container
     *
     * @throws BadMethodCallException
     *
     * @return void
     */
    public function merge(ContainerInterface $container)
    {
        if ($this->isLocked() || $container->isLocked()) {
            throw ContainerLockedException::mergeException();
        }

        $this->parameters->merge($container->getParameters());
        $this->definitions = array_merge((array)$this->definitions, (array)$container->getDefinitions());

        $this->services = array_merge($container->getServices(), $this->services);

        foreach ($container->getAliases() as $alias => $id) {
            $this->setAlias($alias, $id);
        }
    }

    /**
     * resolveId
     *
     * @param mixed $id
     *
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
     * @return string
     */
    public static function camelCaseStr($str)
    {
        return StringHelper::strCamelCaseAll($str, ['_' => ' ', ':' => ' ', '.' => 'Nss ', '\\' => 'Dbs ']);
    }

    /**
     * getReferenceId
     *
     * @param string|object $reference a string or an instance of Reference
     *
     * @return string
     */
    protected function getReferenceId($reference)
    {
        return 0 === strpos($reference, static::SERVICE_REF_INDICATOR) ?
            substr($reference, strlen(static::SERVICE_REF_INDICATOR)) :
            (string)$reference;
    }

    /**
     * buildService
     *
     * @param string $id
     *
     * @throws \Selene\Module\DI\Exception\CircularReferenceException
     * @throws RuntimeException
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
     * @return Object returns a class instance.
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
     * callFactory
     *
     * @param string $class
     * @param string|array $factory
     * @param array $arguments
     *
     * @throws InvalidArgumentException if $factory is unresolvable
     *
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
     * buildFromDefinition
     *
     * @param DefinitionInterface $definition
     * @param DefinitionInterface $parent
     *
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
     * Call the setters on a service instace.
     *
     * @param object $instance the service instance
     * @param array  $setters  the service setters as array.
     *
     * @throws ContainerResolveException
     *
     * @return void
     */
    protected function callSetters($instance, array $setters)
    {
        foreach ($setters as $setter) {

            $method = key($setter);

            $synced = $this->getSyncedArguments($arguments = $setter[$method]);

            // if this caller needs injected services, it will be enqued to the
            // sync callback, otherwise, call the caller directlty.
            if (!empty($synced)) {

                $this->createSyncCallback($instance, $method, $arguments, $synced);
                continue;
            }

            $this->applySetter($instance, $method, $arguments);
        }
    }

    /**
     * applySetter
     *
     * @param object $instance
     * @param string $method
     * @param array  $arguments
     *
     * @throws \BadMethodCallException
     *
     * @return void
     */
    protected function applySetter($instance, $method, $arguments)
    {
        if (!method_exists($instance, $method)) {
            throw ContainerResolveException::setterMethodNotExistent($instance, $method);
        }

        $arguments = $this->getServiceArguments($arguments);

        call_user_func_array([$instance, $method], $arguments);
    }

    /**
     * Call sync callbacks.
     *
     * @param string $id
     *
     * @return void
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
     * @return void
     */
    protected function pushSyncedCallers(array $synced, \Closure $callback)
    {
        foreach ($synced as $serviceId => $sync) {
            $this->synced[$serviceId][] =& $callback;
        }
    }
}
