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
 * @class SchemeMatcher
 * @package Selene\Components\Routing\Matchers
 * @version $Id$
 */
class SchemeMatcher extends AbstractMatcher
{
    /**
     * matches
     *
     * @param Route  $route
     * @param string $scheme
     *
     * @return bool|null
     */
    protected function matchCondition(Route $route, $scheme)
    {
        return in_array($scheme, $route->getSchemes()) ?: null;
    }
}
