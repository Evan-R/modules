<?php

/**
 * This File is part of the Selene\Components\Config package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Config\Validator\Nodes;

/**
 * @class NumericNode extends ScalarNode
 * @see ScalarNode
 *
 * @package Selene\Components\Config
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
abstract class NumericNode extends ScalarNode
{
    protected $min;

    protected $max;

    abstract public function min($value);

    abstract public function max($value);

    /**
     * validate
     *
     * @param mixed $value
     *
     * @access public
     * @return boolean
     */
    public function validate($value = null)
    {
        parent::validate($value);

        $this->validateRange($value);

        return true;
    }

    /**
     * validateRange
     *
     * @param mixed $value
     *
     * @throws \OutOfRangeException if both max and min length constraints do not match
     * @throws \LengthException if max or min length constraints do not match
     * @access protected
     * @return void
     */
    protected function validateRange($value)
    {
        if ($this->min && $this->max) {
            if ($value < $this->min || $value > $this->max) {
                throw new \OutOfRangeException(
                    sprintf('value must be within the range of %s and %s', (string)$this->min, (string)$this->max)
                );
            }
        } elseif ($this->min) {
            if ($value < $this->min) {
                throw new \LengthException(
                    sprintf('value must not deceed %s', (string)$this->min)
                );
            }
        } elseif ($this->max) {
            if ($value > $this->max) {
                throw new \LengthException(
                    sprintf('value must not exceed %s', (string)$this->max)
                );
            }
        }
    }
}
