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

/**
 * @class MatchContext
 * @package Selene\Components\Routing
 * @version $Id$
 */
class MatchContext
{
    private $route;
    private $params;

    public function __construct(Route $route, array $params = [])
    {
        $this->route  = $route;
        $this->params = $params;
    }

    public function setRequest($request)
    {
        $this->request = $request;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function getParameters()
    {
        $params = array_merge($this->route->getDefaults(), $this->params);

        return array_intersect_key($params, array_flip($this->route->getVars()));
    }

    public function getHostParameters()
    {
        return array_intersect_key($this->params, array_flip($this->route->getHostVars()));
    }

    public function getRoute()
    {
        return $this->route;
    }
}
