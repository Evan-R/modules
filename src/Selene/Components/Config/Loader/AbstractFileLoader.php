<?php

/**
 * This File is part of the Selene\Components\Config\Loaders package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Config\Loader;

/**
 * @abstract class AbstractFileLoader implements FileLoaderInterface
 * @see FileLoaderInterface
 * @abstract
 *
 * @package Selene\Components\Config\Loaders
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
abstract class AbstractFileLoader implements FileLoaderInterface
{
    /**
     * @param FileLocatorInterface $locator
     *
     * @access public
     */
    public function __construct(FileLocatorInterface $locator)
    {
        $this->locator = $locator;
    }

    /**
     * import
     *
     * @param mixed $resource
     *
     * @access public
     * @abstract
     * @return mixed
     */
    abstract public function import($resource);

    /**
     * load
     *
     * @access public
     * @abstract
     * @return array
     */
    abstract public function load($resource);

    /**
     * supports
     *
     * @access public
     * @abstract
     * @return string|array
     */
    abstract public function supports($resource);
}
