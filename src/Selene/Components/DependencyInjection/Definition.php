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
 * @class Definition
 * @package Selene\Components\DependencyInjection
 * @version $Id$
 */
class Definition implements DefinitionInterface
{
    private $class;

    private $parent;

    private $arguments;

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
        $this->setScope(Container::SCOPE_CONTAINER);
    }

    public function getClass()
    {
        return $this->class;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function getArguments()
    {
        return $this->arguments;
    }

    public function getScope()
    {
        return $this->scope;
    }

    public function setScope($scope)
    {
        return $this->scope = $scope;
    }

    public function addScope($scope)
    {
        if ($this->scopeIsContainer() and
            (Container::SCOPE_PROTOTYPE === (Container::SCOPE_PROTOTYPE & $scope))
        ) {
            throw new \InvalidArgumentException('cannot add prototype scope to a container scope');
        }

        $this->scope = !$this->scope ? $scope : $this->scope | $scope;
    }

    public function scopeIsContainer()
    {
        return Container::SCOPE_CONTAINER === (Container::SCOPE_CONTAINER & $this->scope);
    }

    public function isResolved()
    {
        return (bool)$this->getResolved();
    }

    public function getResolved()
    {
        return $this->instance;
    }

    public function setResolved($resolved)
    {
        if (!$this->scopeIsContainer()) {
            return;
        }
        $this->instance = $resolved;
    }

    public function addArgument($argument)
    {
        $this->arguments[] = $argument;
        return $this;
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
