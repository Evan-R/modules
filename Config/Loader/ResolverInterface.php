<?php

/**
 * This File is part of the Selene\Module\Config\Resource package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Config\Loader;

/**
 * @interface LoaderResolverInterface
 *
 * @package Selene\Module\Config
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
interface ResolverInterface
{
    /**
     * Resolves a loader for a given resource
     *
     * @param mixed $resource
     *
     * @access public
     * @return \Selene\Module\Config\Resource\LoaderInterface
     */
    public function resolve($resource);

    /**
     * all
     *
     * @return array
     */
    public function all();
}
