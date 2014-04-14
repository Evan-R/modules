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

use \SplStack;
use \SplFixedArray;

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
    const NAME_SEPARATOR = '.';

    const PATH_SEPARATOR = '/';

    const METHOD_GET    = 'GET|HEAD';

    const METHOD_PUT    = 'PUT|PATCH';

    const METHOD_POST   = 'POST';

    const METHOD_DELETE = 'DELETE';

    /**
     * groups
     *
     * @var \SplStack
     */
    protected $groups;

    /**
     * groupRequirements
     *
     * @var \SplStack
     */
    protected $groupRequirements;

    /**
     * groupRequirementKeys
     *
     * @var SplFixedArray
     */
    protected $groupRequirementKeys;

    /**
     * @access public
     * @return mixed
     */
    public function __construct()
    {
        $this->collection = new RouteCollection;
        $this->groups = new SplStack;
        $this->groupRequirements = new SplStack;
    }

    /**
     * make
     *
     * @param mixed $method
     * @param mixed $name
     * @param mixed $path
     * @param array $requirements
     *
     * @access public
     * @return \Selene\Components\Routing\Route
     */
    public function make($method, $name, $path, array $requirements = [])
    {
        $route = new Route(
            $this->getRouteName($name),
            $this->getRoutePath('/'.trim($path, '/')),
            $this->getMethods($method),
            $requirements
        );

        if ($this->inGroup()) {
            $names = $route->setParent($this->concatGroupNames());
            $this->setGroupRequirements($route);
        }

        $this->collection->add($route);

        return $route;
    }

    /**
     * insert
     *
     * @param mixed $parent
     * @param mixed $method
     * @param mixed $name
     * @param mixed $path
     * @param array $requirements
     *
     * @throws \InvalidArgumentException if parent does not exist.
     * @access public
     * @return \Selene\Components\Routing\Route
     */
    public function insert($parent, $method, $name, $path, array $requirements = [])
    {
        if (!($this->collection->has($parent))) {
            throw new \InvalidArgumentException('Cannot insert a route into unknowen parent %s');
        }

        $name   = $parent . static::NAME_SEPARATOR . trim($name, static::NAME_SEPARATOR);
        $path   = $this->collection->get($parent)->getPath() .
            static::PATH_SEPARATOR .
            trim($path, static::PATH_SEPARATOR);

        return $this->make($method, $name, $path, $requirements)->setParent($parent);
    }

    /**
     * getRoutes
     *
     *
     * @access public
     * @return RouteCollectionInterface
     */
    public function getRoutes()
    {
        return $this->collection;
    }

    /**
     * routeGet
     *
     * @param mixed $name
     * @param mixed $path
     * @param array $requirements
     *
     * @access public
     * @return \Selene\Components\Routing\Route
     */
    public function routeGet($name, $path, array $requirements = [])
    {
        return $this->make('GET', $name, $path, $requirements);
    }

    /**
     * routePost
     *
     * @param mixed $name
     * @param mixed $path
     * @param array $requirements
     *
     * @access public
     * @return \Selene\Components\Routing\Route
     */
    public function routePost($name, $path, array $requirements = [])
    {
        return $this->make('POST', $name, $path, $requirements);
    }

    /**
     * routePut
     *
     * @param mixed $name
     * @param mixed $path
     * @param array $requirements
     *
     * @access public
     * @return \Selene\Components\Routing\Route
     */
    public function routePut($name, $path, array $requirements = [])
    {
        return $this->make('PUT', $name, $path, $requirements);
    }

    /**
     * routeDelete
     *
     * @param mixed $name
     * @param mixed $path
     * @param array $requirements
     *
     * @access public
     * @return \Selene\Components\Routing\Route
     */
    public function routeDelete($name, $path, array $requirements = [])
    {
        return $this->make('DELETE', $name, $path, $requirements);
    }

    /**
     * routeAny
     *
     * @param mixed $name
     * @param mixed $path
     * @param array $requirements
     *
     * @access public
     * @return \Selene\Components\Routing\Route
     */
    public function routeAny($name, $path, array $requirements = [])
    {
        return $this->make('GET|HEAD|POST|PUT|PATCH|DELETE', $name, $path, $requirements);
    }

    /**
     * group
     *
     * @param mixed $name
     * @param mixed $root
     * @param mixed $requirements
     * @param callable $builder
     *
     * @access public
     * @return mixed
     */
    public function group($name, $root, $requirements = null, callable $builder = null)
    {
        if (is_callable($requirements)) {
            $builder = $requirements;
            $requirements = null;
        }

        $this->enterGroup($name, $root, $requirements);

        if (null !== $builder) {
            call_user_func_array($builder, [$this]);
            $this->leaveGroup();
        }

        return $this;
    }

    /**
     * getGroupRequirement
     *
     * @access protected
     * @return mixed
     */
    protected function setGroupRequirements(Route $route)
    {
        foreach ($this->groupRequirements->top() as $key => $req) {
            $route->setRequirement($key, $req);
        }
    }

    /**
     * filterGroupRequirements
     *
     * @param mixed $requirements
     *
     * @access protected
     * @return array
     */
    protected function filterGroupRequirements($requirements = null)
    {
        if (null === $requirements) {
            return [];
        }

        $c = $this->getGroupRequirementKeys();

        $req = (array)$requirements;
        $requirements = [];

        foreach ($req as $key => $val) {
            if (in_array($r = '_'.ltrim($key, '_'), $c)) {
                $requirements[$r] = $val;
            }
        }

        return $requirements;
    }


    /**
     * getGroupLevel
     *
     * @access protected
     * @return integer
     */
    protected function getGroupLevel()
    {
        return $this->groups->count();
    }

    /**
     * inGroup
     *
     * @access protected
     * @return boolean
     */
    protected function inGroup()
    {
        return $this->getGroupLevel() > 0;
    }

    /**
     * getRoutePath
     *
     * @param mixed $path
     *
     * @access protected
     * @return string
     */
    protected function getRoutePath($path)
    {
        if ($this->inGroup()) {
            $root = $this->getRealPath();
            $separator = $root === static::PATH_SEPARATOR ? '' : static::PATH_SEPARATOR;
            $path = ltrim($path, static::PATH_SEPARATOR);
        } else {
            $root = '';
            $separator = '';
        }

        return sprintf('%s%s%s', $root, $separator, $path);
    }

    /**
     * concatGroupPaths
     *
     * @access protected
     * @return string
     */
    protected function concatGroupPaths()
    {
        $parts = [];

        foreach ($this->groups as $token) {
            $parts[] =  trim($token['root'], static::PATH_SEPARATOR);
        }

        return implode(static::PATH_SEPARATOR, array_reverse($parts));

    }

    /**
     * concatGroupNames
     *
     * @access protected
     * @return string
     */
    protected function concatGroupNames()
    {
        $parts = [];

        foreach ($this->groups as $token) {
            $parts[] =  trim($token['name'], static::NAME_SEPARATOR);
        }

        return implode(static::NAME_SEPARATOR, array_reverse($parts));
    }

    /**
     * getRouteName
     *
     * @param mixed $name
     *
     * @access protected
     * @return mixed
     */
    protected function getRouteName($name)
    {
        if ($this->inGroup()) {
            $separator = static::NAME_SEPARATOR;
            $baseName = $this->getRealName();
        } else {
            $baseName = '';
            $separator = '';
        }

        return sprintf('%s%s%s', $baseName, $separator, $name);
    }

    /**
     * endGroup
     *
     * @access public
     * @return mixed
     */
    public function endGroup()
    {
        $this->leaveGroup();
    }

    /**
     * enterGroup
     *
     * @param mixed $name
     * @param mixed $root
     *
     * @access protected
     * @return mixed
     */
    protected function enterGroup($name, $root, $requirements = null)
    {
        $rn = $rn = trim($name, static::NAME_SEPARATOR);
        $rp = $rp = trim($root, static::PATH_SEPARATOR);

        $real_name = $this->inGroup() ? $this->concatGroupNames() . static::NAME_SEPARATOR . $rn : $rn;
        $real_path = $this->inGroup() ? $this->concatGroupPaths() . static::PATH_SEPARATOR . $rp : $rp;

        $this->groups->push(compact('name', 'root', 'real_name', 'real_path'));

        $this->groupRequirements->push((array)$requirements);
    }

    /**
     * getRealName
     *
     *
     * @access protected
     * @return string
     */
    protected function getRealName()
    {
        return $this->inGroup() ? $this->groups->top()['real_name'] : '';
    }

    /**
     * getRealPath
     *
     *
     * @access protected
     * @return string
     */
    protected function getRealPath()
    {
        return $this->inGroup() ? $this->groups->top()['real_path'] : '';
    }

    /**
     * leaveGroup
     *
     * @access protected
     * @return void
     */
    protected function leaveGroup()
    {
        if ($this->groups->count()) {
            $this->groups->pop();
            $this->groupRequirements->pop();
        }
    }

    /**
     * flushGroup
     *
     * @access protected
     * @return void
     */
    protected function flushGroup()
    {
        while ($this->groups->count()) {
            $this->groups->pop();
        }
    }

    /**
     * getMethods
     *
     * @param mixed $method
     *
     * @access protected
     * @return mixed
     */
    protected function getMethods($method)
    {
        $methods = explode('|', $method = strtoupper($method));

        if (in_array('GET', $methods) && !in_array('HEAD', $methods)) {
            $methods[] = 'HEAD';
        }

        if (in_array('PUT', $methods) && !in_array('PATCH', $methods)) {
            $methods[] = 'PATCH';
        }

        return $methods;
    }

    /**
     * getGroupRequirementKeys
     * @access protected
     * @return \SplFixedArray
     */
    protected function getGroupRequirementKeys()
    {
        if (!$this->groupRequirementKeys) {
            $req = new SplFixedArray(4);
            $req[] = '_host';
            $req[] = '_schemes';
            $req[] = '_before';
            $req[] = '_after';

            $this->groupRequirementKeys = $req;
        }

        return $this->groupRequirementKeys;
    }
}
