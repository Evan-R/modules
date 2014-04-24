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
class IntergerNode extends NumericNode
{
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
