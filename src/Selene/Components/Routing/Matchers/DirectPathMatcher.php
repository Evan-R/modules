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
 * @class DirectPathMatcher
 * @package Selene\Components\Routing\Matchers
 * @version $Id$
 */
class DirectPathMatcher extends AbstractMatcher
{
    protected function matchCondition(Route $route, $path)
    {
        return 0 === strcmp($path, $route->getStaticPath()) ? true : null;
    }
}
