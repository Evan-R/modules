<?php

/**
 * This File is part of the Selene\Module\Routing\Tests\Loader package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Routing\Tests\Loader;

use \Selene\Module\Routing\RouteCollection;
use \Selene\Module\Routing\Loader\CallableLoader;

/**
 * @class CallableLoaderTest
 * @package Selene\Module\Routing\Tests\Loader
 * @version $Id$
 */
class CallableLoaderTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof(
            '\Selene\Module\Routing\Loader\CallableLoader',
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
