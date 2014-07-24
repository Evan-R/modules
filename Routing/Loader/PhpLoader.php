<?php

/**
 * This File is part of the Selene\Components\Routing package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Routing\Loader;

use \Selene\Components\Config\Loader\PhpFileLoader;
use \Selene\Components\Config\Resource\LocatorInterface;
use \Selene\Components\Routing\RouteCollectionInterface;
use \Selene\Components\Routing\Traits\RoutingLoaderTrait;

/**
 * @class PhpLoader extends ConfigLoader
 * @see ConfigLoader
 *
 * @package Selene\Components\Routing
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class PhpLoader extends PhpFileLoader
{
    use RoutingLoaderTrait;

    /**
     * Constructor.
     *
     * @param RouteCollectionInterface $routes
     */
    public function __construct(RouteCollectionInterface $routes, LocatorInterface $locator)
    {
        $this->newBuilder($routes);
        parent::__construct($locator);
    }

    /**
     * load
     *
     * @param mixed $resource
     *
     * @access public
     * @return mixed
     */
    protected function doLoad($file)
    {
        $routes = $this->getRoutes();

        parent::doLoad($file);
    }

    /**
     * supports
     *
     * @param mixed $format
     *
     * @access public
     * @return boolean
     */
    public function supports($resource)
    {
        return is_string($resource) && 'php' ===  pathinfo(strtolower($resource), PATHINFO_EXTENSION);
    }

    /**
     * {@inheritdoc}
     */
    protected function notifyResource($resource)
    {
    }
}
