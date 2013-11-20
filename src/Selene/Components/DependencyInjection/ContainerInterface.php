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
 * @interface ContainerInterface
 * @package Selene\Components\DependencyInjection
 * @version $Id$
 */
interface ContainerInterface
{
    const SCOPE_CONTAINER = 'container';

    const SCOPE_PROTOTYPE = 'prototype';

    const SERVICE_REF_INDICATOR = '$';

    const APP_CONTAINER_SERVICE = 'app.container';

    public function setParam($param, $definition);

    public function getParam($parameter);

    /**
     * Get the parameter collection of the service.
     *
     * @access public
     * @return Parameters
     */
    public function getParameters();

    /**
     * Return the name of the serviceable container.
     *
     * The name defaults to `staic::APP_CONTAINER_SERVICE`
     *
     * @access public
     * @return string
     */
    public function getName();

    public function setService($service, $class = null, $arguments = null);

    public function getService($service);

    public function getServices();

    public function hasService($service);

    public function merge(ContainerInterface $container);

    public function isLocked();
}
