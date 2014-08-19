<?php

/*
 * This File is part of the Selene\Module\Routing package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Routing\Loader;

use \Selene\Module\Config\Loader\PhpFileLoader;
use \Selene\Module\Config\Resource\LocatorInterface;
use \Selene\Module\Routing\RouteCollectionInterface;
use \Selene\Module\Routing\Traits\RoutingLoaderTrait;

/**
 * @class PhpLoader extends ConfigLoader
 * @see ConfigLoader
 *
 * @package Selene\Module\Routing
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

        include $file;
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
