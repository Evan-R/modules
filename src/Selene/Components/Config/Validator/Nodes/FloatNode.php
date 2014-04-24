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

/**
 * @class FloatNode extends NumericNode
 * @see NumericNode
 *
 * @package Selene\Components\Config\Validator\Nodes
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class FloatNode extends NumericNode
{
    /**
     * Sets a minimum value
     *
     * @param float $value
     *
     * @access public
     * @return FloatNode
     */
    public function min($value)
    {
        $this->min = (float)$value;

        return $this;
    }

    /**
     * Sets a maximum value
     *
     * @param float $value
     *
     * @access public
     * @return FloatNode
     */
    public function max($value)
    {
        $this->max = (float)$value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function validateType($value)
    {
        return is_float($value);
    }

    /**
     * {@inheritdoc}
     */
    protected function getInvalidTypeMessage($value = null)
    {
        return sprintf('%s needs to be type of double, instead saw %s', $this->getKey(), gettype($value));
    }
}
