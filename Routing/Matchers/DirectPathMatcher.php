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
 * @class DirectPathMatcher
 * @package Selene\Module\Routing\Matchers
 * @version $Id$
 */
class DirectPathMatcher extends AbstractMatcher
{
    protected function matchCondition(Route $route, $path)
    {
        return 0 === strcmp($path, $route->getStaticPath()) ? true : null;
    }
}
