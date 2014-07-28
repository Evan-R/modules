<?php

/**
 * This File is part of the Selene\Components\Config\Validator\Exception package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Config\Validator\Exception;

/**
 * @class ValidationException
 * @package Selene\Components\Config\Validator\Exception
 * @version $Id$
 */
class ValidationException extends \InvalidArgumentException
{
    public static function invalidValue($value)
    {
        $value = is_scalar($value) ? (string)$value : json_encode($value);

        return new self(sprintf('Invalid value %s', $value));
    }

    public static function notEmpty($key)
    {
        return new self(sprintf('%s may not be empty.', $key));
    }
}
