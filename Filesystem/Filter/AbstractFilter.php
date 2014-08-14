<?php

/**
 * This File is part of the Selene\Module\Filesystem package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Filesystem\Filter;

/**
 * @class Filter
 * @see FilterInterface
 * @abstract
 *
 * @package Selene\Module\Filesystem
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

    /**
     * __toString
     *
     * @access public
     * @return mixed
     */
    public function __toString()
    {
        return $this->compiled ? $this->compiled : '';
    }

    /**
     * add
     *
     * @param mixed $expression
     *
     * @access public
     * @abstract
     * @return void
     */
    abstract public function add($expression);

    /**
     * doMatch
     *
     * @param mixed $patter
     *
     * @access protected
     * @abstract
     * @return boolean
     */
    abstract protected function doMatch($patter);

    /**
     * compile
     *
     * @access protected
     * @abstract
     * @return void
     */
    abstract protected function compile();
}
