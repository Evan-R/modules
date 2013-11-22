<?php

/**
 * This File is part of the Selene\Components\DI\Loaders package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\DI\Loaders;

/**
 * @class XmlLoader
 * @package Selene\Components\DI\Loaders
 * @version $Id$
 */
class XmlLoader
{
    protected $container;

    /**
     * __construct
     *
     * @param ContainerInterface $container
     *
     * @access public
     * @return mixed
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

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

    }

    private function parseParameters()
    {

    }

    private function parseServices()
    {

    }

    /**
     * supports
     *
     * @param mixed $format
     *
     * @access public
     * @return mixed
     */
    public function supports($format)
    {
        return strtolower($format) === 'xml';
    }
}
