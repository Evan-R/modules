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

use \Selene\Module\Config\Validator\Nodes\NodeInterface;

/**
 * @class InvalidTypeException
 * @package Selene\Module\Config\Validator\Exception
 * @version $Id$
 */
class InvalidTypeException extends ValidationException
{
    public static function invalidType(NodeInterface $node, $value)
    {
        return new self(
            sprintf(
                '%s needs to be type of %s, instead saw %s.',
                $node->getFormattedKey(),
                $node->getType(),
                gettype($value)
            )
        );
    }
}
