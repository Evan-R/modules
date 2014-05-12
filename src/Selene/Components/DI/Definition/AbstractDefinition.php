<?php

/**
 * This File is part of the Selene\Components\DI package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\DI\Definition;

use \Selene\Components\DI\ContainerInterface;
use \Selene\Components\Common\Interfaces\JsonableInterface;

/**
 * @class AbstractDefinition implements DefinitionInterface
 * @see DefinitionInterface
 *
 * @package Selene\Components\DI
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class AbstractDefinition implements DefinitionInterface, \Serializable, JsonableInterface
{
    /**
     * class
     *
     * @var string
     */
    protected $class;

    /**
     * file
     *
     * @var mixed
     */
    protected $file;

    /**
     * parent
     *
     * @var string
     */
    protected $parent;

    /**
     * setters
     *
     * @var array
     */
    protected $setters;

    /**
     * scope
     *
     * @var string
     */
    protected $scope;

    /**
     * factory
     *
     * @var array
     */
    protected $factory;

    /**
     * arguments
     *
     * @var array
     */
    protected $arguments;

    /**
     * injected
     *
     * @var boolean
     */
    protected $injected;

    /**
     * internal
     *
     * @var boolean
     */
    protected $internal;

    /**
     * abstract
     *
     * @var boolean
     */
    protected $abstract;

    /**
     * flags
     *
     * @var array
     */
    protected $flags;

    /**
     * @param mixed $class
     * @param mixed $params
     * @param mixed $scope
     *
     * @access public
     */
    public function __construct($class = null, $arguments = null, $scope = ContainerInterface::SCOPE_CONTAINER)
    {
        $this->class = $this->stripClassName($class);
        $this->scope = $scope;
        $this->arguments = $arguments;
        $this->setters = [];
    }

    /**
     * setClass
     *
     * @param mixed $class
     *
     * @access protected
     * @return DefinitionInterace this instance.
     */
    public function setClass($class)
    {
        $this->class = $this->stripClassName($class);
        return $this;
    }

    /**
     * Get the definition classname
     *
     * @access public
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * setParams
     *
     * @param mixed $class
     *
     * @access public
     * @return DefinitionInterace this instance.
     */
    public function setArguments(array $arguments)
    {
        $this->arguments = $arguments;
        return $this;
    }

    /**
     * addArgument
     *
     * @param mixed $argument
     *
     * @access public
     * @return DefinitionInterace this instance.
     */
    public function addArgument($argument)
    {
        $this->arguments[] = $argument;
        return $this;
    }

    /**
     * get definition arguments
     *
     * @access public
     * @return array
     */
    public function getArguments()
    {
        return (array)$this->arguments;
    }

    /**
     * replaceArgument
     *
     * @param mixed $argument
     * @param mixed $index
     *
     * @access public
     * @return DefinitionInterace this instance.
     */
    public function replaceArgument($argument, $index)
    {
        if (0 > $index || count($this->arguments) < ($index + 1)) {
            throw new \OutOfBoundsException(
                sprintf('Cannot replace argument at index %s, index is out of bounds', $index)
            );
        }

        $this->arguments[$index] = $argument;
        return $this;
    }

    /**
     * hasArguments
     *
     * @access public
     * @return boolean
     */
    public function hasArguments()
    {
        return (bool)$this->arguments;
    }

    /**
     * setParent
     *
     * @param mixed $parent
     *
     * @access public
     * @return DefinitionInterace this instance.
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
        return $this;
    }

    /**
     * Get parent class name
     *
     * @access public
     * @return string|boolean false if no parent class
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * hasParent
     *
     * @access public
     * @return mixed
     */
    public function hasParent()
    {
        return (bool)$this->parent;
    }

    /**
     * getScope
     *
     * @access public
     * @return string
     */
    public function getScope()
    {
        return $this->scope;
    }

    /**
     * setScope
     *
     * @param mixed $scope
     *
     * @access public
     * @return DefinitionInterace this instance.
     */
    public function setScope($scope)
    {
        $this->scope = $scope;
        return $this;
    }

    /**
     * hasScope
     *
     * @param mixed $scope
     *
     * @access public
     * @return bool
     */
    public function hasScope($scope)
    {
        return $scope === ($scope & $this->scope);
    }

    /**
     * addScope
     *
     * @param mixed $scope
     *
     * @throws \InvalidArgumentException
     * @access public
     * @return DefinitionInterace this instance.
     */
    public function addScope($scope)
    {
        if ($this->scopeIsContainer() &&
            (ContainerInterface::SCOPE_PROTOTYPE === (ContainerInterface::SCOPE_PROTOTYPE & $scope))
        ) {
            throw new \InvalidArgumentException('cannot add prototype scope to a container scope');
        }

        $this->scope |= $scope;
        //$this->scope = !$this->scope ? $scope : $this->scope | $scope;
        return $this;
    }

    /**
     * scopeIsContainer
     *
     * @access public
     * @return bool
     */
    public function scopeIsContainer()
    {
        return ContainerInterface::SCOPE_CONTAINER === (ContainerInterface::SCOPE_CONTAINER & $this->scope);
    }

    /**
     * setInjected
     *
     * @param mixed $injected
     *
     * @access public
     * @return DefinitionInterace this instance.
     */
    public function setInjected($injected)
    {
        $this->injected = (bool)$injected;
        return $this;
    }

    /**
     * isInjected
     *
     * @access public
     * @return boolean
     */
    public function isInjected()
    {
        return $this->injected;
    }

    /**
     * addFlag
     *
     * @param mixed $flag
     *
     * @access public
     * @return DefinitionInterace this instance.
     */
    public function addFlag($name, array $arguments = [])
    {
        $flag = new Flag($name, $arguments);
        $this->flags[$flag->getName()] = $flag;

        return $this;
    }

    /**
     * getFlag
     *
     * @param mixed $name
     *
     * @access public
     * @return mixed
     */
    public function getFlag($name)
    {
        return $this->hasFlag($name) ? $this->flags[$name] : null;
    }

    /**
     * hasFlag
     *
     * @param mixed $flag
     *
     * @access public
     * @return boolean
     */
    public function hasFlag($flag)
    {
        return isset($this->flags[$flag]);
    }

    /**
     * hasFlags
     *
     * @access public
     * @return boolean
     */
    public function hasFlags()
    {
        null !== $this->flags;
    }

    /**
     * getFlags
     *
     * @access public
     * @return array
     */
    public function getFlags()
    {
        return $this->flags;
    }

    /**
     * setAbstract
     *
     * @param boolean $abstract
     *
     * @access public
     * @return DefinitionInterace this instance.
     */
    public function setAbstract($abstract)
    {
        $this->abstract = (bool)$abstract;
        return $this;
    }

    /**
     * isAbstract
     *
     * @access public
     * @return mixed
     */
    public function isAbstract()
    {
        return (bool)$this->abstract;
    }

    /**
     * setInternal
     *
     * @param mixed $internal
     *
     * @access public
     * @return DefinitionInterace this instance.
     */
    public function setInternal($internal)
    {
        $this->internal = (bool)$internal;
    }

    /**
     * isInternal
     *
     * @param mixed $internal
     *
     * @access public
     * @return mixed
     */
    public function isInternal()
    {
        return (bool)$this->internal;
    }

    /**
     * setResolved
     *
     * @param mixed $resolved
     *
     * @access public
     * @return DefinitionInterace this instance.
     */
    public function setResolved($resolved)
    {
        if ($this->scopeIsContainer()) {
            $this->resolved = (boolean)$resolved;
        }

        return $this;
    }

    /**
     * isResolved
     *
     * @access public
     * @return bool
     */
    public function isResolved()
    {
        return (bool)$this->getResolved();
    }

    /**
     * setSetters
     *
     * @param array $setters
     *
     * @access public
     * @return DefinitionInterace this instance.
     */
    public function setSetters(array $setters)
    {
        $this->setters = $setters;
        return $this;
    }

    /**
     * addSetter
     *
     * @param string $method
     * @param array $arguments
     *
     * @access public
     * @return DefinitionInterace this instance.
     */
    public function addSetter($method, array $arguments)
    {
        $this->setters[] = [$method => $arguments];
        return $this;
    }

    /**
     * getSetters
     *
     * @access public
     * @return mixed
     */
    public function getSetters()
    {
        return $this->setters;
    }

    /**
     * hasSetters
     *
     * @access public
     * @return mixed
     */
    public function hasSetters()
    {
        return (bool)$this->setters;
    }

    /**
     * setFactory
     *
     * @param mixed $class
     * @param mixed $method
     * @param array $arguments
     *
     * @access public
     * @return mixed
     */
    public function setFactory($class, $method = 'make')
    {
        return $this->setFactoryCallback(null !== $method ? [$class, $method] : $class);
    }

    /**
     * setFactoryCallback
     *
     * @param mixed $factory
     *
     * @access protected
     * @return mixed
     */
    protected function setFactoryCallback($factory)
    {
        if (is_array($factory) || is_string($factory)) {
            $this->factory = $factory;
            return $this;
        }

        throw new \InvalidArgumentException('cannont set factory');
    }

    /**
     * hasFactory
     *
     * @access public
     * @return bool
     */
    public function hasFactory()
    {
        return (bool)$this->factory;
    }

    /**
     * getFactory
     *
     * @access public
     * @return array
     */
    public function getFactory()
    {
        return $this->factory;
    }

    /**
     * setFile
     *
     * @param string $file
     *
     * @access public
     * @return DefinitionInterace this instance.
     */
    public function setFile($file)
    {
        $this->file = $file;
        return $this;
    }

    /**
     * requiresFile
     *
     * @access public
     * @return boolean
     */
    public function requiresFile()
    {
        return (boolean)$this->file;
    }

    /**
     * serialize
     *
     * @access public
     * @return mixed
     */
    public function serialize()
    {
        $data = [
            'class'     => $this->class,
            'parent'    => $this->parent,
            'arguments' => $this->arguments,
            'setters'   => $this->setters,
            'file'      => $this->file,
            'scope'     => $this->scope,
            'factory'   => $this->factory,
            'injected'  => $this->injected,
            'internal'  => $this->internal,
            'abstract'  => $this->abstract,
        ];

        return json_encode($data);
    }

    /**
     * unserialize
     *
     * @param array $data
     *
     * @access public
     * @return mixed
     */
    public function unserialize($data)
    {
        $data = json_decode($data, true);

        $this->class     = $data['class'];
        $this->parent    = $data['parent'];
        $this->arguments = $data['arguments'];
        $this->setters   = $data['setters'];
        $this->file      = $data['file'];
        $this->scope     = $data['scope'];
        $this->factory   = $data['factory'];
        $this->injected  = $data['injected'];
        $this->internal  = $data['internal'];
        $this->abstract  = $data['abstract'];
    }

    /**
     * toJson
     *
     * @access public
     * @return mixed
     */
    public function toJson()
    {
        return json_encode($this->serialize());
    }

    /**
     * stripClassName
     *
     * @param mixed $classname
     *
     * @access private
     * @return mixed
     */
    private function stripClassName($classname)
    {
        // comoposer autoload will fail if a class is requested multiple times, but with mixed NS separators
        return strtr($classname, ['\\\\' => '\\']);
    }
}
