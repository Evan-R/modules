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

use \Selene\Components\Common\Traits\Getter;

/**
 * @class RouteBuilder
 *
 * @package Selene\Components\Routing
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class RouteBuilder
{
    use Getter;

    /**
     * routes
     *
     * @var \Selene\Components\Routing\RouteCollectionInterface
     */
    protected $routes;

    /**
     * actionMap
     *
     * @var array
     */
    protected $actionMap;

    /**
     * @param RouteCollectionInterface $routes
     *
     * @access public
     * @return mixed
     */
    public function __construct(RouteCollectionInterface $routes = null)
    {
        $this->routes = $routes ?: new RouteCollection;
        $this->initGroups();
    }

    /**
     * Create a new Route
     *
     * @param mixed $method
     * @param mixed $name
     * @param mixed $pattern
     * @param array $requirements
     *
     * @access public
     * @return Route
     */
    public function define($method, $name, $pattern, array $requirements = [])
    {
        $route = new Route(
            $name,
            $this->prefixPattern($pattern),
            $this->getMethods($method),
            $this->extendRequirements($requirements)
        );

        $this->add($route);

        return $route;
    }

    /**
     * getRoutes
     *
     * @access public
     * @return RouteCollectionInterface
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * prefixPattern
     *
     * @param mixed $pattern
     *
     * @access protected
     * @return string
     */
    protected function prefixPattern($pattern)
    {
        if (!$this->hasGroups()) {
            return '/'.trim($pattern, '/');
        }

        $prefix = $this->getCurrentGroup()->getPrefix();
        return ('/' === $prefix ? $prefix : (rtrim($prefix, '/').'/')) . trim($pattern, '/');
    }

    /**
     * extendRequirements
     *
     * @param mixed $requirements
     *
     * @access protected
     * @return array
     */
    protected function extendRequirements($requirements)
    {
        if (!$this->hasGroups()) {
            return $requirements;
        }

        return array_merge_recursive($this->getCurrentGroup()->getRequirements(), $requirements);
    }

    /**
     * resource
     *
     * @param mixed $name
     * @param mixed $pattern
     * @param mixed $resource
     *
     * @access public
     * @return RouteBuilder
     */
    public function resource($path, $resource, $actions = [], $resourceConstrait = null)
    {
        $actions = empty($actions) ? $this->getDefaultActions() : $actions;

        foreach ($actions as $action) {

            list ($pattern, $controllerAction, $name) = $this->getResourcePaths(trim($path, '/'), strtolower($action));

            $route = $this->define(
                $this->getResourceActionVerb($action),
                $name,
                $pattern,
                ['_action' => $resource.':'.$controllerAction]
            );

            if (is_string($resourceConstrait)) {
                $route->setConstraint('resource', $resourceConstrait);
            }
        }
    }

    /**
     * getResourcePaths
     *
     * @param mixed $path
     * @param mixed $action
     *
     * @access protected
     * @return array
     */
    protected function getResourcePaths($path, $action)
    {
        $name = strtr($path, ['/' => '_']);

        switch ($action) {
            case 'new':
            case 'index':
                return ['/'.$path, $action, $name.'.'.$action];
            case 'create':
                return ['/'.$path . '/create', $action, $name.'.'.$action];
            case 'show':
            case 'update':
            case 'delete':
                return ['/'.$path . '/{resource}', $action, $name.'.'.$action];
            case 'edit':
                return ['/'.$path . '/edit/{resource}', $action, $name.'.'.$action];
        }
    }

    /**
     * getDefaultMethods
     *
     * @access protected
     * @return array
     */
    protected function getDefaultActions()
    {
        return array_keys($this->getResourceActionMap());
    }

    /**
     * getResourceActionMap
     *
     * @access protected
     * @return array
     */
    protected function getResourceActionMap()
    {
        if (null === $this->actionMap) {
            $this->actionMap = [
                'index'  => 'GET',
                'create' => 'GET',
                'new'    => 'POST',
                'show'   => 'GET',
                'edit'   => 'GET',
                'update' => 'PUT',
                'delete' => 'DELETE',
            ];
        }

        return $this->actionMap;
    }

    /**
     * getResourceActionMap
     *
     * @access protected
     * @return string
     */
    protected function getResourceActionVerb($action)
    {
        return $this->getDefault($this->getResourceActionMap(), $action, 'GET');
    }

    /**
     * group
     *
     * @param mixed $prefix
     * @param array $requirements
     *
     * @access public
     * @return mixed
     */
    public function group($prefix, $requirements = [], $groupConstructor = null)
    {
        if (is_callable($requirements)) {
            $groupConstructor = $requirements;
            $requirements = [];
        }

        $this->enterGroup($prefix, $requirements);

        if (is_callable($groupConstructor)) {
            call_user_func($groupConstructor, $this);
        }
    }

    /**
     * Add a route to the collection
     *
     * @param Route $route
     *
     * @access public
     * @return RouteBuilder
     */
    public function add(Route $route)
    {
        $this->routes->add($route);

        return $this;
    }

    /**
     * getMethods
     *
     * @param mixed $method
     *
     * @access protected
     * @return array
     */
    protected function getMethods($method)
    {
        if (is_array($method)) {
            $method = implode('|', $method);
        }

        $methods = explode('|', strtoupper($method));

        if (in_array('GET', $methods)) {
            $methods[] = 'HEAD';
        }

        if (in_array('PUT', $methods)) {
            $methods[] = 'PATCH';
        }

        return array_unique($methods);
    }

    /**
     * fixRequirements
     *
     * @param array $requirements
     *
     * @access protected
     * @return array
     */
    protected function fixRequirements(array $requirements)
    {
        //$keys = strtr(implode('|'array_keys($requirements)), '_' => '');
        //$values = array_values($requirements);

        //$keys = explode(
        return $requirements;
    }

    /**
     * enterGroup
     *
     *
     * @access protected
     * @return RouteBuilder
     */
    protected function enterGroup($prefix, array $requirements)
    {
        $group = new GroupDefinition($prefix, $requirements, $this->getParentGroup());
        $this->pushGroup($group);

        return $this;
    }

    /**
     * getParentGroup
     *
     *
     * @access protected
     * @return null|GroupDefinition
     */
    protected function getParentGroup()
    {
        if ($this->hasGroups()) {
            return $this->groups->top();
        }
    }

    /**
     * leaveGroup
     *
     * @access protected
     * @return RouteBuilder
     */
    protected function leaveGroup()
    {
        return $this;
    }

    /**
     * @access protected
     * @return mixed
     */
    protected function initGroups()
    {
        $this->groups = new \SplStack;
    }

    /**
     * pushGroup
     *
     * @param array $group
     *
     * @access protected
     * @return void
     */
    protected function pushGroup(GroupDefinition $group)
    {
        $this->groups->push($group);
    }

    /**
     * popGroup
     *
     * @access protected
     * @return array
     */
    protected function popGroup()
    {
        return $this->groups->pop();
    }

    /**
     * getCurrentGroup
     *
     * @access protected
     * @return mixed
     */
    protected function getCurrentGroup()
    {
        return $this->groups->top();
    }

    /**
     * hasGroups
     *
     * @access protected
     * @return boolean
     */
    protected function hasGroups()
    {
        return $this->groups->count() > 0;
    }
}
