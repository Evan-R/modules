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
 * @class IntergerNode
 * @package Selene\Components\Config\Validator\Nodes
 * @version $Id$
 */
class IntegerNode extends NumericNode
{
    /**
     * Sets a minimum value
     *
     * @param int $value
     *
     * @access public
     * @return IntergerNode
     */
    public function min($value)
    {
        $this->min = (int)$value;

        return $this;
    }

    /**
     * Sets a maximum value
     *
     * @param int $value
     *
     * @access public
     * @return IntegerNode
     */
    public function max($value)
    {
        $this->max = (int)$value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function validateType($value)
    {
        return is_int($value);
    }

    /**
     * getInvalidTypeMessage
     *
     * @param mixed $value
     *
     * @access protected
     * @return string
     */
    protected function getInvalidTypeMessage($value = null)
    {
        return sprintf('%s needs to be type of integer, instead saw %s', $this->getKey(), gettype($value));
    }
}
