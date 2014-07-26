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
 * @class IntegerNode extends NumericNode
 * @see NumericNode
 *
 * @package Selene\Components\Config
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
class IntegerNode extends NumericNode
{
    protected $type = self::T_INTEGER;

    /**
     * {@inheritdoc}
     */
    public function validateType($value)
    {
        return is_int($value);
    }
}
