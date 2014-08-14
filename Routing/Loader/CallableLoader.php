<?php

/**
 * This File is part of the Selene\Module\Routing package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Routing\Loader;

use \Selene\Module\Routing\RouteCollectionInterface;
use \Selene\Module\Routing\Traits\RoutingLoaderTrait;
use \Selene\Module\Config\Loader\CallableLoader as BaseCallableLoader;

/**
 * @class CallableLoader extends RoutingLoader
 * @see RoutingLoader
 *
 * @package Selene\Module\Routing
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
