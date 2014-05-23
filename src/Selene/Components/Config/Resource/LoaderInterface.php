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
 * @interface LoaderInterface
 *
 * @package Selene\Components\Config\Resource
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
interface LoaderInterface
{
    /**
     * load
     *
     * @param mixed $resource
     *
     * @access public
     * @return mixed
     */
    public function load($resource, $any = false);

    /**
     * import
     *
     * @param mixed $resource
     *
     * @access public
     * @return mixed
     */
    public function import($resource);

    /**
     * supports
     *
     * @param mixed $resource
     *
     * @access public
     * @return boolean
     */
    public function supports($resource);

    /**
     * setResolver
     *
     * @param LoaderResolverInterface $resolver
     *
     * @access public
     * @return string
     */
    public function setResolver(LoaderResolverInterface $resolver);

    /**
     * getResolver
     *
     * @access public
     * @return \Selene\Components\Config\Resource\LoaderResolverInterface
     */
    public function getResolver();

    /**
     * getResourcePath
     *
     * @access public
     * @return string
     */
    public function getResourcePath();

    public function setResourcePath($path);
}
