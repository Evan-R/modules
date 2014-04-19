<?php

/**
 * This File is part of the Selene\Components\DI package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\DI\Loader;

use \Selene\Components\Config\Loader\ConfigLoader;

/**
 * @class CallableLoader extends ConfigLoader
 * @see ConfigLoader
 *
 * @package Selene\Components\DI
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class CallableLoader extends ConfigLoader
{
    /**
     * Load a callable entity resuorce.
     *
     * @param callable $resource
     *
     * @access public
     * @return void
     */
    public function load($resource)
    {
        $this->loadFromCallable($resource);
    }

    /**
     * {@inheritdoc}
     * @param callable $format
     */
    public function supports($type)
    {
        return is_callable($type);
    }

    /**
     * Exeutes the callable resource.
     *
     * @param callable $callable
     *
     * @access private
     * @return void
     */
    private function loadFromCallable(callable $callable)
    {
        $resource = $this->findResourceOrigin($callable);

        $this->container->addFileResource($resource);

        call_user_func($callable, $this->container);
    }

    /**
     * Find the filepath of the callable entity.
     *
     * @param callable $callable
     *
     * @access private
     * @return string
     */
    private function findResourceOrigin(callable $callable)
    {
        $reflection = is_array($callable) ? new \ReflectionObject($callable[0]) :
            ((is_string($callable) && false !== strpos($callable, '::')) ? new \ReflectionMethod($callable) :
            new \ReflectionFunction($callable));

        return $reflection->getFileName();
    }
}
