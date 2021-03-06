<?php

/**
 * This File is part of the Selene\Module\DI package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\DI\Definition;

use \Selene\Module\DI\ContainerInterface;
use \Selene\Module\DI\Meta\Data as MetaData;
use \Selene\Module\DI\Meta\MetaDataInterface;

/**
 * @class AbstractDefinition implements DefinitionInterface
 * @see DefinitionInterface
 *
 * @package Selene\Module\DI
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
class AbstractDefinition implements DefinitionInterface
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
    protected $meta;

    /**
     * @param mixed $class
     * @param mixed $arguments
     * @param mixed $scope
     *
     * @access public
     */
    public function __construct($class = null, array $arguments = [], $scope = ContainerInterface::SCOPE_CONTAINER)
    {
        $class && $this->setClass($class);

        $this->scope     = $scope;
        $this->arguments = $arguments;
        $this->setters   = [];
        $this->meta      = [];
    }

    /**
     * setClass
     *
     * @param mixed $class
     *
     * @access protected
     * @return DefinitionInterface this instance.
     */
    public function setClass($class)
    {
        if (null !== $class) {
            $this->class = $this->paddClassName($this->stripClassName($class));
        } else {
            $this->class = $class;
        }

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
        return $this->class ?: null;
    }

    /**
     * setParams
     *
     * @param array $arguments
     *
     * @access public
     * @return DefinitionInterface this instance.
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
     * @return DefinitionInterface this instance.
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
     * getArgument
     *
     * @param int $index
     *
     * @throws \OutOfBoundsException if index exceeds argument count.
     * @return mixed
     */
    public function getArgument($index)
    {
        if (!array_key_exists($i = (int)$index, ($args = $this->getArguments()))) {
            throw new \OutOfBoundsException(
                sprintf('Cannot get argument at index %s, index is out of bounds', $i)
            );
        }

        return $args[$i];
    }

    /**
     * replaceArgument
     *
     * @param mixed $argument
     * @param mixed $index
     *
     * @throws \OutOfBoundsException if index exceeds argument count.
     * @return DefinitionInterface this instance.
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
     * @return DefinitionInterface this instance.
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
     * @return DefinitionInterface this instance.
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
     * @return DefinitionInterface this instance.
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
     * addMetaData
     *
     * @param mixed $tagName
     * @param array $parameters
     *
     * @access public
     * @return DefinitionInterface this instance.
     */
    public function setMetaData($tagName, array $parameters = [])
    {
        $data = new MetaData($tagName, $parameters);
        $this->meta[$data->getName()] = $data;

        return $this;
    }

    /**
     * getFlag
     *
     * @param mixed $name string or null
     *
     * @access public
     * @return MetaDataInterface|array|null
     */
    public function getMetaData($name = null)
    {
        return null !== $name ? ($this->hasMetaData($name) ? $this->meta[$name] : []) : (array)$this->meta;
    }

    /**
     * removeMetaData
     *
     * @param mixed $tagName
     *
     * @access public
     * @return DefinitionInterface this instance.
     */
    public function removeMetaData($tagName)
    {
        unset($this->meta[$tagName]);

        return $this;
    }

    /**
     * hasFlag
     *
     * @param mixed $name string or null
     *
     * @access public
     * @return boolean
     */
    public function hasMetaData($name = null)
    {
        return null !== $name ? isset($this->meta[$name]) : (bool) $this->meta;
    }

    /**
     * setAbstract
     *
     * @param boolean $abstract
     *
     * @access public
     * @return DefinitionInterface this instance.
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
     * @return boolean
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
     * @return DefinitionInterface this instance.
     */
    public function setInternal($internal)
    {
        $this->internal = (bool)$internal;

        return $this;
    }

    /**
     * isInternal
     *
     * @access public
     * @return boolean
     */
    public function isInternal()
    {
        return (bool)$this->internal;
    }

    /**
     * setSetters
     *
     * @param array $setters
     *
     * @access public
     * @return DefinitionInterface this instance.
     */
    public function setSetters(array $setters)
    {
        $this->setters = [];

        foreach ($setters as $setter) {
            $method = key($setter);
            $this->addSetter($method, $setter[$method]);
        }

        return $this;
    }

    /**
     * addSetter
     *
     * @param string $method
     * @param array $arguments
     *
     * @access public
     * @return DefinitionInterface this instance.
     */
    public function addSetter($method, array $arguments = [])
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
    public function getSetters($setter = null)
    {
        if (null === $setter) {
            return $this->setters;
        }

        return array_filter($this->setters, function ($s) use ($setter) {
            return $setter === key($s);
        });
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
     * hasSetter
     *
     * @param mixed $method
     *
     * @access public
     * @return boolean
     */
    public function hasSetter($method)
    {
        foreach ($this->setters as $setter) {
            if ($method === key($setter)) {
                return true;
            }
        }

        return false;
    }

    /**
     * setFactory
     *
     * @param mixed $class
     * @param mixed $method
     *
     * @access public
     * @return DefinitionInterface this instance.
     */
    public function setFactory($class, $method = null)
    {
        $this->setFactoryCallback(null !== $method ? [$class, $method] : $class);

        return $this;
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
     * @return DefinitionInterface this instance.
     */
    public function setFile($file)
    {
        $this->file = $file;
        return $this;
    }

    /**
     * getFile
     *
     * @access public
     * @return string
     */
    public function getFile()
    {
        return $this->file;
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
     * merge
     *
     * @param DefinitionInterface $definition
     *
     * @access public
     * @return DefinitionInterface this instance.
     */
    public function merge(DefinitionInterface $definition)
    {
        $this->setScope($definition->getScope());
        $this->setFile($definition->getFile() ?: $this->file);
        $this->setClass($definition->getClass() ?: $this->class);
        $this->setParent($definition->getParent() ?: $this->parent);

        $this->setInjected($definition->isInjected() ? true : $this->injected);
        $this->setInternal($definition->isInternal() ? true : $this->internal);
        $this->setAbstract($definition->isAbstract() ? true : $this->abstract);

        $this->factory = $definition->getFactory() ?: $this->factory;

        foreach ($definition->getSetters() as $setter) {
            $method = key($setter);
            $this->addSetter($method, $setter[$method]);
        }

        $count = count($this->getArguments());

        foreach ($definition->getArguments() as $index => $argument) {
            if ($index < $count) {
                $this->replaceArgument($argument, $index);
            } else {
                $this->addArgument($argument);
            }
        }

        foreach ($definition->getMetaData() as $key => $data) {
            $this->meta[$key] = [];
            $this->meta[$key][] = $data;
        }

        return $this;
    }

    protected function paddClassName($class)
    {
        return '\\'.ltrim($class, '\\');
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
        // composer autoload will fail if a class is requested multiple times, but with mixed NS separators
        return strtr($classname, ['\\\\' => '\\']);
    }
}
