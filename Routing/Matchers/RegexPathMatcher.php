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
 * @class RegexPathMatcher
 * @package Selene\Module\Routing\Matchers
 * @version $Id$
 */
class RegexPathMatcher extends AbstractMatcher
{
    /**
     * matches
     *
     * @param Route  $route
     * @param string $staticPath
     *
     * @return boolean|null
     */
    protected function matchCondition(Route $route, $path)
    {
        return ((bool)preg_match($route->getRegexp(), $path, $matches)) ? $matches : null;
    }
}
