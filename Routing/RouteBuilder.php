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
 * Creates Routes on a route collection.
 *
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
     * Constructor.
     *
     * @param RouteCollectionInterface $routes
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
     * @return Route
     */
    public function define($method, $name, $pattern, $controller, array $requirements = [])
    {
        $route = new Route(
            $name,
            $this->prefixPattern($pattern),
            $this->getMethods($method),
            $this->extendRequirements($this->extractShortcutArgs($controller, $requirements))
        );

        $this->add($route);

        return $route;
    }

    /**
     * Starts a new entry point for grouping routes.
     *
     * @param string $prefix
     * @param array $requirements
     *
     * @return void
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
            $this->leaveGroup();
        }
    }

    /**
     * Ends the group.
     *
     * @return RouteBuilder
     */
    public function endGroup()
    {
        if ($this->hasGroups()) {
            $this->leaveGroup();
        }

        return $this;
    }

    /**
     * Add a route to the collection
     *
     * @param Route $route
     *
     * @return RouteBuilder
     */
    public function add(Route $route)
    {
        $this->routes->add($route);

        return $this;
    }

    /**
     * Add a bunch of routes to the collection.
     *
     * @param RouteCollectionInterface $routes
     *
     * @return RoutBuilder
     */
    public function addRoutes(RouteCollectionInterface $routes)
    {
        $this->routes->merge($routes);
        return $this;
    }

    /**
     * Get the route collection.
     *
     * @return RouteCollectionInterface
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * Define a route using the GET http verb.
     *
     * @param string $name
     * @param string $pattern
     * @param mixed $controller
     * @param mixed $requirements
     *
     * @return Route
     */
    public function get($name, $pattern, $controller, $requirements = [])
    {
        return $this->define('GET', $name, $pattern, $controller, $requirements);
    }

    /**
     * Define a route using the POST http verb.
     *
     * @param mixed $name
     * @param mixed $pattern
     * @param mixed $controller
     * @param mixed $requirements
     *
     * @return Route
     */
    public function post($name, $pattern, $controller, $requirements = [])
    {
        return $this->define('POST', $name, $pattern, $controller, $requirements);
    }

    /**
     * Define a route using the PUT http verb.
     *
     * @param mixed $name
     * @param mixed $pattern
     * @param mixed $controller
     * @param mixed $requirements
     *
     * @return Route
     */
    public function put($name, $pattern, $controller, $requirements = [])
    {
        return $this->define('PUT', $name, $pattern, $controller, $requirements);
    }

    /**
     * Define a route using the DELETE http verb.
     *
     * @param mixed $name
     * @param mixed $pattern
     * @param mixed $controller
     * @param mixed $requirements
     *
     * @return Route
     */
    public function delete($name, $pattern, $controller, $requirements = [])
    {
        return $this->define('DELETE', $name, $pattern, $controller, $requirements);
    }

    /**
     * Define a route using any of the GET, POST, PUT, DELETE http verbs.
     *
     * @param mixed $name
     * @param mixed $pattern
     * @param mixed $controller
     * @param mixed $requirements
     *
     * @return Route
     */
    public function any($name, $pattern, $controller, $requirements = [])
    {
        return $this->define(
            'GET|POST|PUT|DELETE',
            $name,
            $pattern,
            $controller,
            $requirements
        );
    }

    /**
     * resource
     *
     * @param string $path
     * @param string $resource
     * @param array  $actions
     * @param array  $resourceConstrait
     *
     * @return RouteBuilder
     */
    public function resource($path, $resource, $actions = [], $resourceConstrait = null)
    {
        $this->validateActions((array)$actions);

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

    private function validateActions(array $actions = [])
    {
        if (!(bool)$actions) {
            return;
        }

        $defaults = $this->getDefaultActions();


        foreach ($actions as $action) {
            if (!in_array($action, $defaults)) {
                throw new \InvalidArgumentException(
                    sprintf('Invalid action name "%s", action must be %s', $action, $this->lexDefaults($defaults))
                );
            }
        }
    }

    private function lexDefaults(array $defaults)
    {
        $defaults = explode(',', '"'.implode('","', $defaults).'"');

        if (1 < ($c = count($defaults))) {
            $last = array_pop($defaults);
            $defaults[] = 'or ' . $last;
        }

        return implode(', ', $defaults);
    }

    /**
     * extractShortcutArgs
     *
     * @param mixed $controller
     * @param mixed $requirements
     *
     * @return array
     */
    protected function extractShortcutArgs($controller, $requirements = [])
    {
        if (is_array($controller)) {
            return $controller;
        }

        $requirements['_action'] = $controller;

        return $requirements;
    }

    /**
     * prefixPattern
     *
     * @param mixed $pattern
     *
     * @return string
     */
    protected function prefixPattern($pattern)
    {
        if (!$this->hasGroups()) {
            return '/'.trim($pattern, '/');
        }

        $prefix = $this->getCurrentGroup()->getPrefix();

        return rtrim(('/' === $prefix ? $prefix : (rtrim($prefix, '/').'/')) . trim($pattern, '/'), '/');
    }

    /**
     * extendRequirements
     *
     * @param mixed $requirements
     *
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
     * getResourcePaths
     *
     * @param mixed $path
     * @param mixed $action
     *
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
     * @return array
     */
    protected function getDefaultActions()
    {
        return array_keys($this->getResourceActionMap());
    }

    /**
     * getResourceActionMap
     *
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
     * @return string
     */
    protected function getResourceActionVerb($action)
    {
        return $this->getDefault($this->getResourceActionMap(), $action, 'GET');
    }

    /**
     * getMethods
     *
     * @param mixed $method
     *
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
     * @return RouteBuilder
     */
    protected function leaveGroup()
    {
        if ($this->hasGroups()) {
            $this->popGroup();
        }

        return $this;
    }

    /**
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
     * @return void
     */
    protected function pushGroup(GroupDefinition $group)
    {
        $this->groups->push($group);
    }

    /**
     * popGroup
     *
     * @return array
     */
    protected function popGroup()
    {
        return $this->groups->pop();
    }

    /**
     * getCurrentGroup
     *
     * @return mixed
     */
    protected function getCurrentGroup()
    {
        return $this->groups->top();
    }

    /**
     * hasGroups
     *
     * @return boolean
     */
    protected function hasGroups()
    {
        return $this->groups->count() > 0;
    }
}
