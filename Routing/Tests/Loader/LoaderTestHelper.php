<?php

/**
 * This File is part of the Selene\Components\Routing\Tests\Loader package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Routing\Tests\Loader;

use \Mockery as m;

/**
 * @class LoaderTestHelper
 * @package Selene\Components\Routing\Tests\Loader
 * @version $Id$
 */
trait LoaderTestHelper
{
    protected $locator;

    protected $builder;

    protected $routes;

    protected function mockBuilder()
    {
        return m::mock('Selene\Components\DI\BuilderInterface');
    }

    protected function mockLocator()
    {
        return m::mock('Selene\Components\Config\Resource\LocatorInterface');
    }

    protected function mockRoutes()
    {
        return m::mock('Selene\Components\Routing\RouteCollectionInterface');
    }
}
