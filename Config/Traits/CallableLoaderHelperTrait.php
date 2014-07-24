<?php

/**
 * This File is part of the Selene\Components\DI\Traits package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Config\Traits;

/**
 * @trait CallableResourceOriginTrait
 *
 * @package Selene\Components\DI\Traits
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
trait CallableLoaderHelperTrait
{
    /**
     * Find the filepath of the callable entity.
     *
     * @param callable $callable
     *
     * @return string
     */
    private function findResourceOrigin(callable $callable)
    {
        $reflection = is_array($callable) ? new \ReflectionObject($callable[0]) :
            ((is_string($callable) && false !== strpos($callable, '::')) ? new \ReflectionMethod($callable) :
            new \ReflectionFunction($callable));

        return $reflection->getFileName();
    }

    /**
     * Checks the resource being a callable.
     *
     * @param mixed $resource
     *
     * @return boolean
     */
    public function supports($resource)
    {
        return is_callable($resource);
    }

}
