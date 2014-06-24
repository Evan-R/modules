<?php

/**
 * This File is part of the Selene\Components\Routing\Matchers package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Routing\Matchers;

use \Selene\Components\Routing\Route;

/**
 * @class RegexPathMatcher
 * @package Selene\Components\Routing\Matchers
 * @version $Id$
 */
class RegexPathMatcher extends AbstractMatcher
{

    /**
     * matches
     *
     * @param Route $route
     * @param mixed $staticPath
     *
     * @access public
     * @return mixed
     */
    protected function matchCondition(Route $route, $path)
    {
        return ((bool)preg_match($route->getRegexp(), $path, $matches)) ? $matches : null;
    }
}
