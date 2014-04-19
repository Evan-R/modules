<?php

/**
 * This File is part of the Selene\Components\Config package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Config\Validation\Exception;

/**
 * @class InvalidValueException extends \InvalidArgumentException
 * @see \InvalidArgumentException
 *
 * @package Selene\Components\Config\Validation\Exception
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class InvalidTypeException extends \InvalidArgumentException
{
    public function __construct($value, $type)
    {
        $this->createMessage($value, $type);
    }

    protected function createMessage($value, $type)
    {
        $this->message = sprintf('node needs to be type of %s, but intead saw, %s', $type, $value);
    }
}
