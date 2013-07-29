<?php

/**
 * This File is part of the Selene\Components\Filesystem package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Filesystem\Filter;

/**
 * @class Filter
 * @see FilterInterface
 * @abstract
 *
 * @package Selene\Components\Filesystem
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com
 * @license MIT
 */
abstract class AbstractFilter implements FilterInterface
{
    /**
     * match
     *
     * @param mixed $pattern
     *
     * @access public
     * @return boolean
     */
    public function match($pattern)
    {
        $this->compile();
        return $this->doMatch($pattern);
    }

    /**
     * not
     *
     * @param mixed $pattern
     *
     * @access public
     * @return boolean
     */
    public function not($pattern)
    {
        return !$this->match($pattern);
    }

    abstract protected function doMatch($patter);

    abstract protected function compile();
}
