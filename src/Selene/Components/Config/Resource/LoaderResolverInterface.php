<?php

/**
 * This File is part of the Selene\Components\Config\Resource package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Config\Resource;

/**
 * @interface LoaderResolverInterface
 *
 * @package Selene\Components\Config
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
interface LoaderResolverInterface
{
    /**
     * Resolves a loader for a given resource
     *
     * @param mixed $resource
     *
     * @access public
     * @return \Selene\Components\Config\Resource\LoaderInterface
     */
    public function resolve($resource);
}
