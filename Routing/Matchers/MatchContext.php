<?php

/**
 * This File is part of the Selene\Components\Routing package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Routing\Matchers;

use \Selene\Components\Routing\Route;
use \Symfony\Component\HttpFoundation\Request;

/**
 * @class MatchContext
 * @package Selene\Components\Routing
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
     * Constructor.
     *
     * @param Route $route
     * @param array $params
     */
    public function __construct(Route $route, array $params = [], Request $request = null)
    {
        $this->route   = $route;
        $this->params  = $params;
        $this->request = $request;
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
}
