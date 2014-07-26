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
 * @class LengthException
 * @package Selene\Components\Config\Validator\Exception
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
class LengthException extends \LengthException
{
    /**
     * exceedsLength
     *
     * @param mixed $max
     *
     * @return LengthException
     */
    public static function exceedsLength($max)
    {
        return new self(sprintf('Value must not exceed %s.', (string)$max));
    }

    /**
     * deceedsLength
     *
     * @param mixed $min
     *
     * @return LengthException
     */
    public static function deceedsLength($min)
    {
        return new self(sprintf('Value must not deceed %s.', (string)$min));
    }
}
