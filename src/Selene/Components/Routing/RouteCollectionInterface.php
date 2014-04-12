<?php

/**
 * This File is part of the Selene\Components\Routing package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Routing;

/**
 * @interface RouteCollectionInterface
 *
 * @package Selene\Components\Routing
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
interface RouteCollectionInterface
{
    /**
     * add
     *
     * @param Route $route
     * @param mixed $name
     * @param mixed $overrideName
     *
     * @access public
     * @return void
     */
    public function add(Route $route, $name = null, $overrideName = false);

    /**
     * get
     *
     * @param mixed $name
     *
     * @access public
     * @return Selene\Components\Routing\Route
     */
    public function get($name);

    /**
     * findByMethod
     *
     * @param mixed $method
     *
     * @access public
     * @return Selene\Components\Routing\CollectionInterface
     */
    public function findByMethod($method);

    /**
     * all
     *
     * @access public
     * @return mixed
     */
    public function raw();
}
