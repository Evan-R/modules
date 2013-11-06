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

/**
 * @class Definition implements DefinitionInterface Definition
 * @see DefinitionInterface
 *
 * @package Selene\Components\DependencyInjection
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class Definition implements DefinitionInterface
{
    /**
     * class
     *
     * @var string
     */
    private $class;

    /**
     * parent
     *
     * @var string|boolean
     */
    private $parent;

    /**
     * setters
     *
     * @var array|null
     */
    private $setters;

    /**
     * factory
     *
     * @var array|null
     */
    private $factory;

    /**
     * arguments
     *
     * @var array
     */
    private $arguments;

    /**
     * instance
     *
     * @var object|null
     */
    private $instance;

    /**
     * @param mixed $class
     * @param mixed $params
     * @param mixed $scope
     *
     * @access public
     */
    public function __construct($class = null, $arguments = null)
    {
        $this->setClass($class);
        $this->setArguments($arguments);
        $this->setScope(ContainerInterface::SCOPE_CONTAINER);
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
     * get definition arguments
     *
     * @access public
     * @return array
     */
    public function getArguments()
    {
        return $this->arguments;
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
     * setScope
     *
     * @param mixed $scope
     *
     * @access public
     * @return void
     */
    public function setScope($scope)
    {
        $this->scope = $scope;
        return $this;
    }

    /**
     * addScope
     *
     * @param mixed $scope
     *
     * @throws \InvalidArgumentException
     * @access public
     * @return void
     */
    public function addScope($scope)
    {
        if ($this->scopeIsContainer() and
            (ContainerInterface::SCOPE_PROTOTYPE === (ContainerInterface::SCOPE_PROTOTYPE & $scope))
        ) {
            throw new \InvalidArgumentException('cannot add prototype scope to a container scope');
        }

        $this->scope = !$this->scope ? $scope : $this->scope | $scope;
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
     * getResolved
     *
     * @access public
     * @return mixed|object
     */
    public function getResolved()
    {
        return $this->instance;
    }

    /**
     * setResolved
     *
     * @param mixed $resolved
     *
     * @access public
     * @return void
     */
    public function setResolved($resolved)
    {
        if (!$this->scopeIsContainer()) {
            return;
        }
        $this->instance = $resolved;
    }

    /**
     * addArgument
     *
     * @param mixed $argument
     *
     * @access public
     * @return mixed
     */
    public function addArgument($argument)
    {
        $this->arguments[] = $argument;
        return $this;
    }

    /**
     * addSetter
     *
     * @param string $method
     * @param array $arguments
     *
     * @access public
     * @return void
     */
    public function addSetter($method, array $arguments)
    {
        $this->setters[] = compact('method', 'arguments');
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
    public function setFactory($class, $method)
    {
        $this->factory = compact('class', 'method');
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
     * setClass
     *
     * @param mixed $class
     *
     * @access protected
     * @return void
     */
    protected function setClass($class = null)
    {
        if (null !== $class) {
            $this->parent = get_parent_class($class);
        }
        $this->class = $class;
    }

    /**
     * setParams
     *
     * @param mixed $class
     *
     * @access protected
     * @return void
     */
    protected function setArguments($arguments)
    {
        $this->arguments = is_array($arguments) ? $arguments : [];
    }
}
