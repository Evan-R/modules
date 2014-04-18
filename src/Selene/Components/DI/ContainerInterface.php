<?php

/**
 * This File is part of the Selene\Components\DI package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\DI;

use \Selene\Components\DI\Resolve\ResolveStrategyCollection;

/**
 * @interface ContainerInterface
 * @package Selene\Components\DI
 * @version $Id$
 */
interface ContainerInterface
{
    const SCOPE_CONTAINER = 'container';

    const SCOPE_PROTOTYPE = 'prototype';

    const SERVICE_REF_INDICATOR = '$';

    const APP_CONTAINER_SERVICE = 'app.container';

    ///**
    // * Return the name of the serviceable container.
    // *
    // * The name defaults to `staic::APP_CONTAINER_SERVICE`
    // *
    // * @access public
    // * @return string
    // */
    //public function getName();

    //public function setParameter($param, $definition);

    //public function getParameter($parameter);

    ///**
    // * Get the parameter collection of the service.
    // *
    // * @access public
    // * @return Parameters
    // */
    public function getParameters();

    public function setAlias($alias, $id);

    public function getAlias($alias);

    public function addFileResource($file);

    public function define($service, $class = null, array $arguments = [], $scope = self::SCOPE_CONTAINER);

    public function get($id);

    public function inject($id, $instance, $scope = self::SCOPE_CONTAINER);

    public function getDefinitions();

    public function hasDefinition($id);

    public function merge(ContainerInterface $container);

    public function isLocked();

    public function compile(ResolveStrategyCollection $strategies = null);
}
