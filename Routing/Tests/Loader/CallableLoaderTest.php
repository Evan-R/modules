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

use \Selene\Components\Routing\RouteCollection;
use \Selene\Components\Routing\Loader\CallableLoader;

/**
 * @class CallableLoaderTest
 * @package Selene\Components\Routing\Tests\Loader
 * @version $Id$
 */
class CallableLoaderTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof(
            '\Selene\Components\Routing\Loader\CallableLoader',
            new CallableLoader(
                new RouteCollection
            )
        );
    }

    /** @test */
    public function itShouldCallCallbackWithRoutesAsArgument()
    {
        $called = false;
        $loader = new CallableLoader($routes = new RouteCollection);

        $loader->load(function ($rb) use (&$routes, &$called) {
            $called = true;
            $this->assertSame($routes, $rb->getRoutes());
        });

        $this->assertTrue($called, 'Callback should be called.');
    }
}
