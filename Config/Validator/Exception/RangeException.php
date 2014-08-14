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
 * @class RangeException
 * @package Selene\Module\Config\Validator\Exception
 * @version $Id$
 */
class RangeException extends \RangeException
{
    public static function outOfRange($min, $max)
    {
        return new self(
            sprintf('Value must be within the range of %s and %s.', (string)$min, (string)$max)
        );
    }
}
