<?php

/**
 * This File is part of the Selene\Module\Config\Validator\Exception package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Config\Validator\Exception;

/**
 * @class MissingValueException
 * @package Selene\Module\Config\Validator\Exception
 * @version $Id$
 */
class MissingValueException extends ValidationException
{
    public static function missingValue($key)
    {
        return new self(sprintf('%s is required but missing.', $key));
    }

    public static function emptyValue($key)
    {
        return new self(sprintf('%s is required but empty.', $key));
    }

    public static function missingDefault($key)
    {
        return new self(
            sprintf('optional key %s with empty value must have a default value', $key)
        );
    }
}
