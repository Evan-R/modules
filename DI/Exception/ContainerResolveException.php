<?php

/**
 * This File is part of the Selene\Module\DI package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\DI\Exception;

/**
 * @class ContainerResolveException extends \ErrorException
 * @see \ErrorException
 *
 * @package Selene\Module\DI
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class ContainerResolveException extends \ErrorException
{
    public static function setterMethodNotExistent($instance, $method)
    {
        $class = $instance instanceof Definition ?
            $instance->getClass() :
            (is_object($instance) ? get_class($instance) : $instance);

        return new self(sprintf('Method %s::%s() does not exist', $class, $method));
    }
}
