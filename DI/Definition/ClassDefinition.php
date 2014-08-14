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

/**
 * @class ClassDefinition extends ServiceDefinition
 * @see ServiceDefinition
 *
 * @package Selene\Module\DI
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class ClassDefinition extends ServiceDefinition
{
    /**
     * Constructor.
     *
     * @param string $class
     * @param array  $arguments
     * @param int    $scope
     */
    public function __construct($class, array $arguments = [], $scope = ContainerInterface::SCOPE_PROTOTYPE)
    {
        $this->setClass($class);
        $this->setArguments($arguments);
        $this->scope = $scope;
        $this->setters = [];
    }

    /**
     * {@inheritdoc}
     */
    public function setInjected($injected)
    {
        if ((bool)$injected && false === $this->scopeIsContainer()) {
            throw new \LogicException('Cannot inject a class that has not container scope.');
        }

        return call_user_func([$this, __NAMESPACE__.'\\AbstractDefinition::setInjected'], $injected);
    }

    /**
     * {@inheritdoc}
     */
    public function setScope($scope)
    {
        if ($this->isInjected() && ContainerInterface::SCOPE_PROTOTYPE === $scope) {
            throw new \LogicException('Cannot set prototype scope on an injected class.');
        }

        return call_user_func([$this, __NAMESPACE__.'\\AbstractDefinition::setScope'], $scope);
    }

    /**
     * {@inheritdoc}
     */
    public function setClass($class)
    {
        if (!is_string($class)) {
            throw new \InvalidArgumentException(sprintf('Class must be type string, instead saw', gettype($class)));
        }
        return parent::setClass($class);
    }
}
