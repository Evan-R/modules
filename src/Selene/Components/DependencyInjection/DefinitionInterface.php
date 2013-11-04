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
 * @interface DefinitionInterface
 * @package Selene\Components\DependencyInjection
 * @version $Id$
 */
interface DefinitionInterface
{
    public function getClass();
    public function getParent();

    public function addArgument($argument);
    public function getArguments();

    public function setScope($scope);
    public function addScope($scope);
    public function hasScope($scope);
    public function getScope();

    public function addSetter($method, array $arguments);
    public function getSetters();
    public function hasSetters();

    public function setFactory($class, $method);
    public function hasFactory();
    public function getFactory();

    public function scopeIsContainer();
    public function isResolved();
    public function getResolved();
    public function setResolved($resolved);
}
