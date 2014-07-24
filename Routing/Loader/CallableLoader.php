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

use \Selene\Components\Routing\RouteCollectionInterface;
use \Selene\Components\Routing\Traits\RoutingLoaderTrait;
use \Selene\Components\Config\Loader\CallableLoader as BaseCallableLoader;

/**
 * @class CallableLoader extends RoutingLoader
 * @see RoutingLoader
 *
 * @package Selene\Components\Routing
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class CallableLoader extends BaseCallableLoader
{
    use RoutingLoaderTrait;

    /**
     * Constrictor.
     *
     * @param RouteCollectionInterface $routes
     */
    public function __construct(RouteCollectionInterface $routes)
    {
        $this->newBuilder($routes);
    }

    /**
     * {@inheritdoc}
     */
    public function doLoad($resource, $any = false)
    {
        call_user_func($resource, $this->getRoutes());
    }

    /**
     * {@inheritdoc}
     */
    protected function notifyResource($resource)
    {
    }
}
