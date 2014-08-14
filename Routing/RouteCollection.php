<?php

/**
 * This File is part of the Selene\Module\Routing package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Routing;

use \Serializable;
use \JsonSerializable;
use \IteratorAggregate;
use \Selene\Module\Common\Traits\Getter;
use \Selene\Module\Common\Data\AbstractList;

/**
 * @class RouteCollection
 * @package Selene\Module\Routing
 * @version $Id$
 */
class RouteCollection implements RouteCollectionInterface, IteratorAggregate, Serializable
{
    use Getter;

    /**
     * routes
     *
     * @var mixed
     */
    protected $routes;

    /**
     * @access public
     * @return mixed
     */
    public function __construct()
    {
        $this->routes = [];
    }

    /**
     * merge
     *
     * @param mixed $
     *
     * @access public
     * @return mixed
     */
    public function merge(RouteCollection $routes)
    {
        $this->routes = array_merge($this->routes, $routes->raw());
    }

    /**
     * add
     *
     * @param Route $route
     * @param mixed $name
     * @param mixed $overrideName
     *
     * @return void
     */
    public function add(Route $route, $name = null, $overrideName = false)
    {
        $this->routes[$name && $overrideName ? $name : $route->getName()] = $route;
        $route->collection($this);
    }

    /**
     * all
     *
     * @access public
     * @return mixed
     */
    public function raw()
    {
        return $this->routes;
    }

    /**
     * get
     *
     * @param mixed $name
     *
     * @access public
     * @return mixed
     */
    public function get($name)
    {
        return $this->getDefault($this->routes, $name);
    }

    /**
     * has
     *
     * @param mixed $name
     *
     * @access public
     * @return boolean
     */
    public function has($name)
    {
        return isset($this->routes[$name]);
    }

    /**
     * findByMethod
     *
     * @access public
     * @return \Selene\Module\Routing\RouteCollection
     */
    public function findByMethod($method)
    {
        $method = strtoupper($method);
        $collection = $this->create();

        foreach ($this->routes as $name => $route) {
            if (in_array($method, $route->getMethods())) {
                $collection->add($route, $name);
            }
        }

        return $collection;
    }

    /**
     * create
     *
     * @access protected
     * @return mixed
     */
    protected function create()
    {
        return new static;
    }

    /**
     * getIterator
     *
     * @access public
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->routes);
    }

    /**
     * serialize
     *
     * @access public
     * @return string
     */
    public function serialize()
    {
        return serialize($this->routes);
    }

    /**
     * unserialize
     *
     * @param mixed $data
     *
     * @access public
     * @return \Selene\Module\Routing\RouteCollectionInterface this
     * instance
     */
    public function unserialize($data)
    {
        $this->routes = unserialize($data);
    }
}
