<?php

/**
 * This File is part of the Selene\Components\DI\Exception package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\DI\Exception;

/**
 * @class ContainerLockedException extends \BadMethodCallException
 * @see \BadMethodCallException
 *
 * @package Selene\Components\DI\Exception
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
class ContainerLockedException extends \BadMethodCallException
{
    public static function replaceParameterException()
    {
        return new self('Can\'t replace parameters on a locked container.');
    }

    public static function setDefinitionException($id)
    {
        return new self(sprintf('Cannot set definition "%s" on a locked container.', $id));
    }

    public static function mergeException()
    {
        return new self('Cannot merge a locked container.');
    }
}
