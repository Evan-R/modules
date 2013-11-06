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

    public function setParam($param, $definition);

    public function getParam($parameter);

    public function getParameters();

    public function setService($service, $class = null, $arguments = null);

    public function getService($service);

    public function getServices();

    public function hasService($service);

    public function merge(ContainerInterface $container);

    public function isLocked();
}
