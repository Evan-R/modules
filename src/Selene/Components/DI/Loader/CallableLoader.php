<?php

/**
 * This File is part of the Selene\Components\DI\Loader package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\DI\Loader;

/**
 * @class CallableLoader
 * @package Selene\Components\DI\Loader
 * @version $Id$
 */
class CallableLoader extends ConfigLoader
{
    /**
     * load
     *
     * @param mixed $resource
     *
     * @access public
     * @return mixed
     */
    public function load($resource)
    {
        $this->loadFromCallable($resource);
    }

    /**
     * supports
     *
     * @param mixed $type
     *
     * @access public
     * @return boolean
     */
    public function supports($type)
    {
        return is_callable($type);
    }

    /**
     * loadFromCallable
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
     * findResourceOrigin
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
