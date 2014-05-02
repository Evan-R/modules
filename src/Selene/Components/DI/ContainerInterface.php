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
 *
 * @package Selene\Components\DI
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
interface ContainerInterface
{
    const SCOPE_CONTAINER = 'container';

    const SCOPE_PROTOTYPE = 'prototype';

    const SERVICE_REF_INDICATOR = '$';

    /**
     * setParameter
     *
     * @param mixed $param
     * @param mixed $definition
     *
     * @access public
     * @return void
     */
    public function setParameter($param, $definition);

    /**
     * getParameter
     *
     * @param mixed $parameter
     *
     * @access public
     * @return mixed
     */
    public function getParameter($parameter);

    /**
     * getParameters
     *
     *
     * @access public
     * @return ParameterInterface
     */
    public function getParameters();

    /**
     * setAlias
     *
     * @param mixed $alias
     * @param mixed $id
     *
     * @access public
     * @return void
     */
    public function setAlias($alias, $id);

    /**
     * getAlias
     *
     * @param mixed $alias
     *
     * @access public
     * @return string
     */
    public function getAlias($alias);

    /**
     * Define a service
     *
     * @param mixed $service
     * @param mixed $class
     * @param array $arguments
     * @param mixed $scope
     *
     * @access public
     * @return mixed
     */
    public function define($service, $class = null, array $arguments = [], $scope = self::SCOPE_CONTAINER);

    /**
     * Resolve a service instance
     *
     * @param mixed $id
     *
     * @access public
     * @return Object
     */
    public function get($id);

    /**
     * inject a service instance
     *
     * @param mixed $id
     * @param mixed $instance
     * @param mixed $scope
     *
     * @access public
     * @return void
     */
    public function inject($id, $instance, $scope = self::SCOPE_CONTAINER);

    /**
     * getDefinition
     *
     * @param mixed $id
     *
     * @access public
     * @return DefinitionInterface
     */
    public function getDefinition($id);

    /**
     * getDefinitions
     *
     * @access public
     * @return array
     */
    public function getDefinitions();

    /**
     * hasDefinition
     *
     * @param mixed $id
     *
     * @access public
     * @return boolean
     */
    public function hasDefinition($id);

    /**
     * Merge two containers.
     *
     * @param ContainerInterface $container
     *
     * @access public
     * @return mixed
     */
    public function merge(ContainerInterface $container);

    /**
     * isLocked
     *
     * @access public
     * @return booelean
     */
    public function isLocked();
}
