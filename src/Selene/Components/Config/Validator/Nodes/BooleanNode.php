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
 * @class BooleanNode extends Node
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
     * @access public
     * @return mixed
     */
    public function __construct()
    {
        parent::__construct('boolean');
    }

    /**
     * defaultValue
     *
     * @param mixed $val
     *
     * @access public
     * @return mixed
     */
    public function defaultValue($val)
    {
        return parent::defaultValue((bool)$val);
    }

    /**
     * validateType
     *
     * @param mixed $value
     *
     * @access public
     * @return mixed
     */
    public function validateType($value)
    {
        return is_bool($value);
    }
}
