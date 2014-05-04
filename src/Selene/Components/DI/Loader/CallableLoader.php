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

use \Selene\Components\DI\BuilderInterface;
use \Selene\Components\Config\Resource\Loader;
use \Selene\Components\Config\Resource\LocatorInterface;

/**
 * @class CallableLoader extends Loader
 * @see ConfigLoader
 *
 * @package Selene\Components\DI
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class CallableLoader extends Loader
{

    /**
     * @param BuilderInterface $builder
     * @param LocatorInterface $locator
     *
     * @access public
     */
    public function __construct(BuilderInterface $builder, LocatorInterface $locator)
    {
        parent::__construct($locator);
        $this->container = $builder->getContainer();
        $this->builder = $builder;
    }

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

        $this->builder->addFileResource($resource);

        call_user_func($callable, $this->builder);
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
