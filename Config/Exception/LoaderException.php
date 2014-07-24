<?php

/**
 * This File is part of the Selene\Components\Config\Exception package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Config\Exception;

/**
 * @class LoaderException
 * @package Selene\Components\Config\Exception
 * @version $Id$
 */
class LoaderException extends \RuntimeException
{

    /**
     * invalidResource
     *
     * @param mixed $resource
     *
     * @return LoaderException
     */
    public static function missingLoader($resource)
    {
        return new self(
            sprintf('No acceptable loader found for resrouce "%s".', static::formatResource($resource))
        );
    }

    /**
     * invalidResource
     *
     * @param mixed $resource
     *
     * @return LoaderException
     */
    public static function invalidResource($resource)
    {
        return new self(
            sprintf('Invalid resrouce "%s".', static::formatResource($resource))
        );
    }

    /**
     * formatResource
     *
     * @param mixed $resource
     *
     * @return string
     */
    private static function formatResource($resource)
    {
        return is_object($resource) ? get_class($resource) :
            (is_string($resource) ? $resource : 'type '.gettype($resource));
    }
}
