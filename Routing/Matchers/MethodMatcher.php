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
 * @class MethodMatcher
 * @package Selene\Module\Routing\Matchers
 * @version $Id$
 */
class MethodMatcher extends AbstractMatcher
{
    /**
     * matchCondition
     *
     * @param Route $route
     * @param mixed $method
     *
     * @access protected
     * @return mixed
     */
    protected function matchCondition(Route $route, $method)
    {
        return in_array($method, $route->getMethods()) ? true : null;
    }
}
