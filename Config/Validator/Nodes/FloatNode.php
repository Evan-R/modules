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
 * @class FloatNode extends NumericNode
 * @see NumericNode
 *
 * @package Selene\Components\Config
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
class FloatNode extends NumericNode
{
    protected $type = self::T_FLOAT;

    /**
     * {@inheritdoc}
     */
    public function validateType($value)
    {
        return is_float($value);
    }
}
