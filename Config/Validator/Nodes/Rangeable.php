<?php

/**
 * This File is part of the Selene\Components\Config\Validator\Nodes package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Config\Validator\Nodes;

use \Selene\Components\Config\Validator\Exception\RangeException;
use \Selene\Components\Config\Validator\Exception\LengthException;

/**
 * @class Rangeable
 * @package Selene\Components\Config\Validator\Nodes
 * @version $Id$
 */
trait Rangeable
{
    /**
     * min
     *
     * @var mixed
     */
    protected $min;

    /**
     * max
     *
     * @var mixed
     */
    protected $max;

    /**
     * Set a minimum value.
     *
     * @param mixed $value a numeric value
     *
     * @return ScalarNode
     */
    public function min($value)
    {
        $this->min = $value;
    }

    /**
     * Set a maximum value.
     *
     * @param mixed $value a numeric value
     *
     * @return ScalarNode
     */
    public function max($value)
    {
        $this->max = $value;
    }
    /**
     * validateRange
     *
     * @param mixed $value a numeric value
     *
     * @throws RangeException if both max and min length constraints do not match
     * @throws LengthException if max or min length constraints do not match
     *
     * @return void
     */
    protected function checkRange($value)
    {
        if (null !== $this->min && null !== $this->max) {
            if ($this->min > $value || $this->max < $value) {
                throw RangeException::outOfRange($this->min, $this->max);
            }
        } elseif (null !== $this->min && $value < $this->min) {
            throw LengthException::deceedsLength($this->min);
        } elseif (null !== $this->max && $value > $this->max) {
            throw LengthException::exceedsLength($this->max);
        }
    }
}
