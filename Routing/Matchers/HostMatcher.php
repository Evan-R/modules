<?php

/**
 * This File is part of the Selene\Module\Routing\Matchers package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Routing\Matchers;

use \Selene\Module\Routing\Route;

/**
 * @class HostMatcher
 * @package Selene\Module\Routing\Matchers
 * @version $Id$
 */
class HostMatcher extends AbstractMatcher
{
    /**
     * matches
     *
     * @param Route $route
     * @param mixed $requirement
     *
     * @access public
     * @return bool
     */
    public function matches(Route $route, $requirement)
    {
        if (null !== $route->getHost()) {
            return parent::matches($route, $requirement);
        }

        return true;
    }
    /**
     * matches
     *
     * @param Route $route
     * @param mixed $staticPath
     *
     * @access public
     * @return mixed
     */
    protected function matchCondition(Route $route, $host)
    {
        return (bool)preg_match($route->getHostRegexp(), $host, $matches) ? $matches : null;
    }
}
