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

use \Selene\Components\Config\Validator\Exception\RangeException;
use \Selene\Components\Config\Validator\Exception\LengthException;

/**
 * @class NumericNode extends ScalarNode
 * @see ScalarNode
 *
 * @package Selene\Components\Config
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
abstract class NumericNode extends ScalarNode
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
     * {@inheritdoc}
     */
    public function validate()
    {
        parent::validate();
        $this->validateRange($this->getValue());

        return true;
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
    protected function validateRange($value)
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

    /**
     * Only let null be treat as empty value.
     *
     * {@inheritdoc}
     */
    protected function isEmptyValue($value = null)
    {
        return null === $value;
    }
}
