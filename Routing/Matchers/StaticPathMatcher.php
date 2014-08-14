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
 * @class StaticPathMatcher
 * @package Selene\Module\Routing\Matchers
 * @version $Id$
 */
class StaticPathMatcher extends AbstractMatcher
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
        return 0 === strpos($path, $route->getStaticPath()) ? true : null;
    }
}
