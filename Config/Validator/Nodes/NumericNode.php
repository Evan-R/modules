<?php

/**
 * This File is part of the Selene\Module\Config package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Config\Validator\Nodes;

/**
 * @class NumericNode extends ScalarNode
 * @see ScalarNode
 *
 * @package Selene\Module\Config
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
abstract class NumericNode extends ScalarNode
{
    use Rangeable;

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
        $this->checkRange($value);
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
