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
 * @class BooleanNode extends ScalarNode
 * @see Node
 *
 * @package Selene\Module\Config
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class BooleanNode extends ScalarNode
{
    const C_TRUE = 'true';
    const C_FALSE = 'false';

    /**
     * type
     *
     * @var string
     */
    protected $type = self::T_BOOL;

    /**
     * {@inheritdoc}
     */
    public function defaultValue($val)
    {
        return parent::defaultValue((bool)$val);
    }

    /**
     * {@inheritdoc}
     */
    public function validateType($value)
    {
        return is_bool($value);
    }

    /**
     * isEmptyValue
     *
     * @param mixed $value
     *
     * @return boolean
     */
    protected function isEmptyValue($value = null)
    {
        return is_string($value) ? (!strlen(trim($value)) > 0) : null === $value;
    }
}
