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
 * @class BooleanNode extends ScalarNode
 * @see Node
 *
 * @package Selene\Components\Config
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class BooleanNode extends ScalarNode
{
    /**
     * Create a new boolean node.
     * @access public
     */
    public function __construct()
    {
        parent::__construct('boolean');
    }

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
     * @access protected
     * @return boolean
     */
    protected function isEmptyValue($value = null)
    {
        return is_string($value) ? (!strlen(trim($value)) > 0) : null === $value;
    }
}
