<?php

/**
 * This File is part of the Selene\Module\Routing\Cache package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Routing\Cache;

use \Selene\Module\Routing\RouteCollection;
use \Selene\Module\Routing\StaticRouteCollection;

/**
 * @class CachedRoutes extends StaticRouteCollection
 * @see StaticRouteCollection
 *
 * @package Selene\Module\Routing\Cache
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class CachedRoutes extends StaticRouteCollection
{
    public function __construct(Storage $storage)
    {
        $this->storage = $storage;
        $collection = $this->storage->read();

        parent::__construct($collection);
    }
}
