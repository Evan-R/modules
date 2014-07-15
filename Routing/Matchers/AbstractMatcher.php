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
 * @class AbstractMatcher
 * @package Selene\Components\Routing\Matchers
 * @version $Id$
 */
abstract class AbstractMatcher implements MatcherInterface
{
    protected $callback;

    /**
     * matchThen
     *
     * @param callable $callback
     *
     * @access public
     * @return void
     */
    public function onMatch(callable $callback)
    {
        $this->callback = $callback;
    }

    /**
     * matches
     *
     * @param Route $route
     * @param mixed $requirement
     *
     * @access public
     * @return boolean
     */
    public function matches(Route $route, $requirement)
    {
        if (null !== ($matches = $this->matchCondition($route, $requirement))) {
            $this->callCallback($route, $matches);

            return true;
        }

        return false;
    }

    /**
     * callCallback
     *
     * @access protected
     * @return mixed
     */
    protected function callCallback()
    {
        if (!$this->callback) {
            return;
        }
        call_user_func_array($this->callback, func_get_args());
    }

    /**
     * matchCondition
     *
     * @param Route $route
     * @param mixed $requirement
     *
     * @access protected
     * @abstract
     * @return mixed
     */
    abstract protected function matchCondition(Route $route, $requirement);
}
