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

use Closure;
use ReflectionClass;
use ReflectionMethod;
use ReflectionFunction;
use InvalidArgumentException;
use Selene\Components\DependencyInjection\Exception\ContainerBindException;

/**
 * Class: Container
 *
 * @implements ContainerInterface
 *
 * @package
 * @version
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class Container implements ContainerInterface
{
    /**
     * parameters
     *
     * @var array
     */
    protected $parameters = [];

    /**
     * aliases
     *
     * @var array
     */
    protected $aliases = [];

    /**
     * bindings
     *
     * @var array
     */
    protected $bindings = [];

    /**
     * arraySet
     *
     * @var mixed
     */
    protected $arraySet;

    /**
     * instances
     *
     * @var array
     */
    protected $instances = [];

    /**
     * reflectionStorage
     *
     * @var array
     */
    protected $reflectionStorage = [];

    /**
     * sharedObjects
     *
     * @var array
     */
    protected $sharedObjects = [];

    /**
     * serviceArguments
     *
     * @var array
     */
    protected $serviceArguments = [];

    /**
     * setterArguments
     *
     * @var array
     */
    protected $setterArguments = [];

    /**
     * isCalling
     *
     * @var mixed
     */
    protected $isCalling = [];

    /**
     * methodMap
     *
     * @var array
     */
    protected $methodMap = [];

    /**
     * lastBound
     *
     * @var string
     */
    protected $lastBound;

    /**
     * resolveId
     *
     * @var string
     */
    protected $resolveId;

    /**
     * bind
     *
     * @param mixed $abstract
     * @param mixed $concrete
     * @param mixed $shared
     *
     * @access public
     * @return Container
     */
    public function bind($identifier, $implementation = null, $shared = false)
    {
        if (is_array($identifier)) {
            $identifier = $this->setAliasFromArray($identifier);
        }

        if ($this->hasGetter($identifier)) {
            throw new ContainerBindException(
                sprintf('%s: can\'t overwrite explicit getter "%s"', $identifier, $this->getGetter($identifier))
            );
        }

        if (is_null($implementation) and !class_exists($identifier)) {
            throw new ContainerBindException(
                sprintf('%s: can\'t bind an empty implementation to an alias', $identifier)
            );
        }

        if (is_string($implementation)) {

            if (interface_exists($identifier)) {

                throw new ContainerBindException(
                    sprintf('[%s] binding to an interface is forbidden.', $implementation)
                );

            } elseif ((class_exists($identifier) and class_exists($implementation))) {
                throw new ContainerBindException(
                    sprintf('can\'t bind exsiting class %s to existing class %s', $implementation, $identifier)
                );
            }
        }

        $this->bindings[$identifier] = $implementation;

        unset($this->bindings[$identifier]);
        $this->lastBound = null;

        if (!$implementation instanceof Closure) {

            $this->lastBound = $identifier;

            $implementation = function ($container) use ($identifier, $implementation) {
                $this->resolveId = $identifier;
                $instance = $identifier === $implementation ?
                    $this->resolve($implementation) : $this->build($implementation);
                $this->resolveId = null;

                return $instance;
            };
        }

        $this->bindings[$identifier] = compact('implementation', 'shared');

        return $this;
    }

    /**
     * bindIf
     *
     * @param mixed $identifier
     * @param mixed $implementation
     * @param mixed $shared
     *
     * @access public
     * @return Container
     */
    public function bindIf($identifier, $implementation, $shared = false)
    {
        if (!$this->isBound($identifier) and !$this->hasGetter($identifier)) {
            return $this->bind($identifier, $implementation, $shared);
        }

        return $this;
    }

    /**
     * build
     *
     * @param mixed $implementation
     * @access public
     * @return mixed
     */
    public function build($implementation, $arguments = [])
    {
        if ($implementation instanceof Closure) {
            return $implementation($this, $arguments);
        }

        $reflection = $this->getReflectionClass($implementation);

        if (!$reflection->isInstantiable()) {
            throw new ContainerBindException(
                sprintf('%s is not instantiable', $implementation)
            );
        }

        if ($reflectionConstructor = $reflection->getConstructor()) {
            $reflectionParams = $reflectionConstructor->getParameters();

            if (!count($reflectionParams)) {
                $instance = new $implementation;
            } else {
                $ar = $arguments;
                $args =& $ar;

                $resolvedParams = (array) $this->resolveServiceArguments($reflectionParams, $arguments,
                    $this->resolveId ? $this->resolveId : $implementation
                );
                $instance = $reflection->newInstanceArgs($resolvedParams);
            }
        } else {
            $instance = $reflection->newInstanceWithoutConstructor();
        }

        return $instance;
    }

    public function alias($identifier, $alias)
    {
        $this->aliases[$alias] = $identifier;
    }

    /**
     * getAlias
     *
     * @param mixed $alias
     * @access public
     * @return string
     */
    public function getAlias($alias)
    {
        return isset($this->aliases[$alias]) ? $this->aliases[$alias] : $alias;
    }

    /**
     * getAliasDefinition
     *
     * @param array $definition
     * @access protected
     * @return array
     */
    protected function getAliasDefinition(array $definition)
    {
        return [key($definition), current($definition)];
    }

    /**
     * hasCallers
     *
     * @param mixed $identifier
     * @access protected
     * @return mixed
     */
    public function hasCallers($identifier)
    {
        return isset($this->isCalling[$identifier]);
    }

    /**
     * singleton
     *
     * @param mixed $identifier
     * @param mixed $implementation
     * @access public
     * @return Container
     */
    public function singleton($identifier, $implementation)
    {
        return $this->bind($identifier, $implementation, true);
    }

    /**
     * resolve
     *
     * @param mixed $identifier
     * @param mixed $arguments
     * @access public
     * @return mixed
     */
    public function resolve($identifier, $arguments = [])
    {
        $identifier = $this->getAlias($identifier);

        // check for explicit getter methods.
        if ($this->hasGetter($identifier)) {
                $method = $this->getGetter($identifier);

                return $this->{$method}();
        }

        // if is shared:
        if (isset($this->instances[$identifier])) {
            return $this->instances[$identifier];
        }

        $implementation = $this->getImplementation($identifier);

        // resolve the object instance
        $instance = $this->isBuildable($identifier, $implementation) ?
            $this->build($implementation, $arguments) :
            $this->resolve($implementation, $arguments);

        if ($this->isShared($identifier)) {
            $this->instances[$identifier] = $instance;
        }

        return $instance;
    }

    /**
     * parameter
     *
     * @param mixed $parameter
     * @param mixed $value
     * @access public
     * @return mixed
     */
    public function parameter($parameter, $value)
    {
        return null;
    }

    /**
     * arguments
     *
     * @param array $arguments
     * @param mixed $binding
     *
     * @access public
     * @return Container
     */
    public function addArguments(array $arguments = [], $binding = null)
    {
        foreach ($arguments as $argument) {
            $this->addArgument($argument, $binding);
        }

        return $this;
    }

    /**
     * addArgument
     *
     * @param mixed $argument
     * @param mixed $current
     *
     * @access public
     * @return Container
     */
    public function addArgument($argument, $binding = null)
    {
        $binding = is_null($binding) ? $this->getLastBound() : $binding;

        $this->serviceArguments[$binding][] = $this->getArgumentResolverCallBack($argument);

        return $this;
    }

    /**
     * instance
     *
     * @param mixed $binding
     * @param mixed $instance
     * @access public
     * @return mixed
     */
    public function instance($binding, $instance)
    {
        if (is_array($binding)) {
            $binding = $this->setAliasFromArray($binding);
        }

        return $this->instances[$binding] = $instance;
    }

    /**
     * check if there’s a binding with that identifier
     *
     * @param  string $identifier
     * @return bool
     */
    public function isBound($identifier)
    {
        return array_key_exists($identifier, $this->bindings);
    }

    /**
     * keys
     *
     * @access public
     * @return array
     */
    public function keys()
    {
        return array_keys($this->bindings);
    }

    /**
     * setters
     *
     * @access public
     * @return mixed
     */
    public function setters()
    {
        return $this->isCalling;
    }

    /**
     * arguments
     *
     * @param mixed $param
     * @access public
     * @return mixed
     */
    public function arguments($param)
    {
        return $this->serviceArguments;
    }

    /**
     * check if there’s a shared object that's been registered with the
     * given identifier
     *
     * @param  string $identifier
     * @return bool
     */
    public function isShared($identifier)
    {
        return $this->isBound($identifier) and $this->bindings[$identifier]['shared'];
    }

    /**
     * share
     *
     * @param Closure $callable
     * @access public
     * @return Closure
     */
    public function share(Closure $callable)
    {
        return function () use ($callable) {

            static $object;

            if (is_null($object)) {
                $object = $callable($this);
            }

            return $object;
        };
    }

    /**
     * extend
     *
     * @param mixed   $identifier
     * @param Closure $callable
     * @access public
     * @return mixed
     */
    public function extend($identifier, Closure $callable)
    {
        if (!$this->isBound($identifier)) {
            throw new InvalidArgumentException(sprintf('%s is not defined', $identifier));
        }

        $constructor = $this->getImplementation($identifier);

        if (!$constructor instanceof Closure) {
            throw new InvalidArgumentException(sprintf('%s contains no definition', $identifier));
        }

        $this->bindings[$identifier]['implementation'] = function () use ($identifier, $constructor, $callable) {
            return $callable($constructor($this), $this);
        };

        return $this;
    }

    /**
     * call
     *
     * @access public
     * @return void
     */
    public function call($method, $arguments = array(), $binding = null)
    {
        $binding = is_null($binding) ? $this->getLastBound() : $binding;

        foreach ((array) $arguments as $arg) {
            $this->addSetterArgument($arg, $method, $binding);
        }

        if (!isset($this->isCalling[$binding])) {

            $this->extend($binding, function ($instance, $container) use ($binding) {
                foreach ($this->isCalling[$binding] as $method) {
                    call_user_func_array([$instance, $method], (array) $this->resolveSetterArguments(
                        $instance, $method, $binding
                    ));
                }

                return $instance;
            });
        }

        $this->isCalling[$binding][] = $method;

        return $this;
    }

    /**
     * hasGetter
     *
     * @param mixed $binding
     * @access public
     * @return bool
     */
    public function hasGetter($identifier)
    {
        if (isset($this->methodMap[$identifier])) {

            if ($this->methodMap[$identifier]['exists']) {
                $method = $this->methodMap[$identifier]['method'];

                return true;
            }

        } elseif (method_exists($this, $method = static::getGetterName($identifier))) {
            $this->methodMap[$identifier]['exists'] = true;
            $this->methodMap[$identifier]['method'] = $method;

            return true;
        } else {
            $this->methodMap[$identifier]['exists'] = false;
        }

        return false;
    }

    /**
     * getGetter
     *
     * @param mixed $binding
     * @access public
     * @return string
     */
    public function getGetter($identifier)
    {
        return $this->methodMap[$identifier]['method'];
    }

    /**
     * offsetSet
     *
     * @param mixed $offset
     * @param mixed $value
     * @access public
     * @return mixed
     */
    public function offsetSet($offset, $value)
    {
        $this->arraySet = $offset;
        if (!$value instanceof Closure) {
            $value = function () use ($value) {
                return $value;
            };
        }

        $this->bind($offset, $value);
        //$this->arraySet = null;
    }

    /**
     * offsetExists
     *
     * @param mixed $offset
     * @access public
     * @return mixed
     */
    public function offsetExists($offset)
    {
        return $this->isBound($offset);
    }

    /**
     * offsetGet
     *
     * @param mixed $offset
     * @access public
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->resolve($offset);
    }

    /**
     * offsetUnset
     *
     * @param mixed $offset
     * @access public
     * @return mixed
     */
    public function offsetUnset($offset)
    {
        unset($this->bindings[$offset]);
    }


    /**
     * getLastBound
     *
     * @access protected
     * @return string
     */
    protected function getLastBound()
    {
        return $this->lastBound;
    }

    /**
     * setAliasFromArray
     *
     * @param array $definition
     * @access protected
     * @return void
     */
    protected function setAliasFromArray(array $definition)
    {
        list($identifier, $alias) = $this->getAliasDefinition($definition);
        $this->alias($identifier, $alias);

        return $identifier;
    }

    /**
     * isBuildable
     *
     * @param mixed $binding
     * @param mixed $implementation
     *
     * @access protected
     * @return bool
     */
    protected function isBuildable($binding, $implementation)
    {
        return $binding === $implementation or $implementation instanceof Closure;
    }

    /**
     * resolveArguments
     *
     * @param mixed $params
     * @param array $resolved
     * @param mixed $binding
     * @access public
     * @return StdClass
     */
    protected function resolveServiceArguments(&$params, array &$resolved = array(), $binding = null)
    {
        $resolved = $this->getArguments($binding, $resolved, $this->serviceArguments);

        return $this->resolveReflectionParameters($params, $resolved);
    }

    /**
     * resolveArgument
     *
     * @param mixed $argument
     * @access protected
     * @return mixed
     */
    protected function resolveArgument(&$argument)
    {
        if ($argument instanceof Closure) {
            $argument = $argument($this);
        } elseif (is_string($argument) and (class_exists($argument) or $this->isBound($argument))) {
            $argument = $this->resolve($argument);
        }

        return $argument;
    }

    /**
     * resolveSetterArguments
     *
     * @param object $instance
     * @param string $method
     * @param string $binding
     *
     * @access protected
     * @return mixed
     */
    protected function resolveSetterArguments($instance, $method, $binding)
    {
        $args = array();
        $resolved = $this->getArguments(
            $this->getSetterBindingName($method, $binding),
            $args,
            $this->setterArguments
        );

        $reflectionMethod = $this->getReflectionMethod($method, $instance);
        $params = $reflectionMethod->getParameters();

        return $this->resolveReflectionParameters($params, $resolved);
    }

    /**
     * getSetterBindingName
     *
     * @param string $method
     * @param string $binding
     *
     * @access protected
     * @return string
     */
    protected function getSetterBindingName($method, $binding)
    {
        return sprintf('%s#%s', $binding, $method);
    }

    /**
     * getArgumentResolverCallBack
     *
     * @param mixed $pool
     * @param mixed $argument
     * @access protected
     * @return Closure
     */
    protected function getArgumentResolverCallBack(&$argument)
    {
        return function () use (&$argument) {
            $arg = $this->resolveArgument($argument);

            return $arg;
        };
    }

    /**
     * getImplementation
     *
     * @param mixed $identifier
     * @access protected
     * @return mixed
     */
    protected function getImplementation($identifier)
    {
        return $this->isBound($identifier) ?
            $this->bindings[$identifier]['implementation'] :
            $identifier;
    }

    /**
     * addSetterArgument
     *
     * @param mixed  $argument
     * @param string $method
     * @param string $binding
     *
     * @access protected
     * @return Container
     */
    protected function addSetterArgument($argument, $method, $binding = null)
    {
        $binding = $this->getSetterBindingName($method, is_null($binding) ? $this->getLastBound() : $binding);

        $this->setterArguments[$binding][] = $this->getArgumentResolverCallBack($argument);

        return $this;
    }

    /**
     * Resolve parameter retrieved from `RefelctionFunctionAbstract#getParameters()`
     *
     * @param  array    $parameter
     * @return stdClass
     */
    protected function resolveReflectionParameters(array &$params, array &$resolved = array())
    {
        foreach ($params as $index => $param) {
            $set = true;
            if (isset($resolved[$index])) {
                $set = false;
            } elseif ($class = $param->getClass()) {
                $res = $this->resolve($class->getName());
            } else {
                $res = $this->resolveReflectionParameter($param);
            }

            if ($set) {
                if ($param->isPassedByReference()) {
                    $resolved[$index] = &$res;
                } else {
                    $resolved[$index] = $res;
                }
            }
        }

        return (object) $resolved;
    }

    /**
     * getArguments
     *
     * @param mixed $identifier
     * @param array $args
     * @param array $pool
     *
     * @access protected
     * @return array
     */
    protected function getArguments($identifier, array &$args = array(), array $pool = array())
    {
        if (array_key_exists($identifier, $pool)) {
            foreach ($pool[$identifier] as $i => $argument) {
                $arg = $argument();
                $args[] = &$arg;
                unset($arg);
            }
        }

        return $args;
    }

    /**
     * getReflectionClass
     *
     * @param mixed $class
     * @access protected
     * @return ReflectionClass
     */
    protected function getReflectionClass($class)
    {
        if (!isset($this->reflectionStorage[$class])) {
            $this->reflectionStorage[$class] = new ReflectionClass($class);
        }

        return $this->reflectionStorage[$class];
    }

    /**
     * getReflectionFunction
     *
     * @param mixed $method
     * @access protected
     * @return ReflectionFunction
     */
    protected function getReflectionFunction(Closure $method, $id)
    {
        if (!isset($this->reflectionStorage[$id])) {
            $this->reflectionStorage[$id] = new ReflectionFunction($method);
        }

        return $this->reflectionStorage[$id];

    }


    /**
     * getReflectionMethod
     *
     * @param string $method
     * @param object $instance
     *
     * @access protected
     * @return ReflectionMethod
     */
    protected function getReflectionMethod($method, $instance)
    {
        $class = get_class($instance);

        if (!isset($this->reflectionStorage[$storage = $class . '#' . $method])) {
            $this->reflectionStorage[$storage] = new ReflectionMethod($instance, $method);
        }

        return $this->reflectionStorage[$storage];
    }

    /**
     * getGetterName
     *
     * @param mixed $binding
     * @access protected
     * @return string
     */
    public static function getGetterName($binding)
    {
        $str = preg_replace_callback('/(\\\|\#|\$|-|\.|\:)/', function ($matches) use ($binding){
            switch ($matches[1]) {
            case '.':
                return '_dot_';
            case ':':
                return '_colon_';
            case '#':
                return '_hash_';
            case '$':
                return '_dollar_';
            case '-':
                return '_dash_';
            case '\\':
                return '%%NS%%';
            }

            return '';
        }, $binding);

        return sprintf('get%sBinding', str_replace('%%NS%%', '_', str_camel_case_all($str)));
    }
}
