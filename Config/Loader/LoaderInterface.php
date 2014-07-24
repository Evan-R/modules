<?php

/**
 * This File is part of the Selene\Components\Config\Loader package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Config\Loader;

/**
 * @interface LoaderInterface
 * @package Selene\Components\Config\Loader
 * @version $Id$
 */
interface LoaderInterface
{
    const LOAD_ALL = true;

    const LOAD_ONE = false;

    /**
     * Load and resolves a resource
     *
     * @param mixed   $resource
     * @param boolean $collect
     *
     * @return void
     */
    public function load($resource, $collect = self::LOAD_ONE);

    /**
     * Import a resource
     *
     * @param mixed $resource
     *
     * @return void
     */
    public function import($resource);

    /**
     * Determine weather this loader supports the resource type.
     *
     * @param mixed $resource
     *
     * @return boolean
     */
    public function supports($resource);

    /**
     * Set the loader resolver.
     *
     * @param ResolverInterface $resolver
     *
     * @return void
     */
    public function setResolver(ResolverInterface $resolver);

    /**
     * Get the loader resolver.
     *
     * @return ResolverInterface
     */
    public function getResolver();

    /**
     * addListener
     *
     * @param LoaderListener $listener
     *
     * @return void
     */
    public function addListener(LoaderListener $listener);

    /**
     * removeListener
     *
     * @param LoaderListener $listener
     *
     * @return void
     */
    public function removeListener(LoaderListener $listener);
}
