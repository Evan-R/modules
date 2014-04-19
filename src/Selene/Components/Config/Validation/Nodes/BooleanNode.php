<?php

/**
 * This File is part of the Selene\Components\Config\Validation\Nodes package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Config\Validation\Nodes;

use \Selene\Components\Config\Validation\Exception\InvalidValueException;

/**
 * @class BooleanNode extends Node
 * @see Node
 *
 * @package Selene\Components\Config\Validation\Nodes
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class BooleanNode extends Node
{
    protected $conditions;

    /**
     * ifTrueThen
     *
     * @param mixed $then
     *
     * @access public
     * @return mixed
     */
    public function ifTrue()
    {
        $this->conditions[1] = [];
        return $this;
    }

    /**
     * ifFalseThen
     *
     * @param mixed $then
     *
     * @access public
     * @return mixed
     */
    public function ifFalse()
    {
        $this->conditions[0] = [];
        return $this;
    }

    public function validate($value = null)
    {
        parent::validate($value);

        if (!is_bool($value)) {
            throw new InvalidTypeException($value, 'boolean');
        }

        return true;
    }
}
