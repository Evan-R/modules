<?php

/**
 * This File is part of the Selene\Module\Routing package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Routing\Matchers;

use \Selene\Module\Routing\Route;
use \Symfony\Component\HttpFoundation\Request;

/**
 * @class MatchContext
 * @package Selene\Module\Routing
 * @version $Id$
 */
class MatchContext
{
    /**
     * route
     *
     * @var Route
     */
    private $route;

    /**
     * params
     *
     * @var array
     */
    private $params;

    /**
     * request
     *
     * @var Request
     */
    private $request;

    /**
     * routeName
     *
     * @var string
     */
    private $routeName;

    /**
     * Constructor.
     *
     * @param Route $route
     * @param array $params
     */
    public function __construct(Route $route, array $params = [], Request $request = null, $name = null, $type = null)
    {
        $this->route       = $route;
        $this->params      = $params;
        $this->request     = $request;
        $this->routeName   = $name ?: $route->getName();
        $this->requestType = $type;
    }

    /**
     * getReqestType
     *
     * @return void
     */
    public function getRequestType()
    {
        return $this->requestType;
    }

    /**
     * setReqestType
     *
     * @param mixed $type
     *
     * @return void
     */
    public function setRequestType($type)
    {
        $this->requestType = $type;
    }

    /**
     * setRequest
     *
     * @param Request $request
     *
     * @return void
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     * getRequest
     *
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * getParameters
     *
     * @return array
     */
    public function getParameters()
    {
        $params = array_merge($this->route->getDefaults(), $this->params);

        return array_intersect_key(
            $params,
            array_merge(array_flip($this->route->getVars()), $this->route->getParameters())
        );
    }

    /**
     * getHostParameters
     *
     * @return array
     */
    public function getHostParameters()
    {
        $params = array_merge($this->route->getHostDefaults(), $this->params);

        return array_intersect_key(
            $params,
            array_merge(array_flip($this->route->getHostVars()), $this->route->getHostDefaults())
        );
    }

    /**
     * getRoute
     *
     * @access public
     * @return Route
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * getRouteName
     *
     * @return string
     */
    public function setRouteName($routeName)
    {
        if (null !== $this->getRoute() && null === $this->getRoute()->getName()) {
            $this->routeName = $routeName;
        }
    }

    /**
     * getRouteName
     *
     * @return string
     */
    public function getRouteName()
    {
        return $this->routeName;
    }
}
