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

/**
 * @interface DefinitionInterface
 * @package Selene\Module\DI
 * @version $Id$
 */
interface DefinitionInterface
{
    public function getClass();
    public function getParent();

    public function addArgument($argument);
    public function getArguments();
    public function getArgument($index);
    public function setArguments(array $arguments);
    public function replaceArgument($argument, $index);

    public function setScope($scope);
    public function addScope($scope);
    public function hasScope($scope);
    public function getScope();

    public function addSetter($method, array $arguments = []);
    public function setSetters(array $setters);
    public function getSetters();
    public function hasSetters();

    public function setFactory($class, $method = null);
    public function hasFactory();
    public function getFactory();

    public function scopeIsContainer();

    public function setInjected($injected);
    public function isInjected();

    public function setInternal($internal);
    public function isInternal();

    public function setAbstract($abstract);
    public function isAbstract();

    public function setMetadata($tagname, array $parameters = []);
    public function getMetaData($tagname = null);
    public function hasMetaData($tagname = null);

    public function setFile($file);
    public function getFile();
    public function requiresFile();
}
