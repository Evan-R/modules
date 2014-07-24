<?php

/**
 * This File is part of the Selene\Components\Routing\Tests\Loader\DI package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Routing\Tests\Loader\DI;

use \Mockery as m;
use \Selene\Components\Routing\RouteCollection;
use \Selene\Components\Routing\Loader\DI\CallableLoader;
use \Selene\Components\Routing\Tests\Loader\LoaderTestHelper;

/**
 * @class CallableLoaderTest
 * @package Selene\Components\Routing\Tests\Loader\DI
 * @version $Id$
 */
class CallableLoaderTest extends \PHPUnit_Framework_TestCase
{
    use LoaderTestHelper;

    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof(
            '\Selene\Components\Routing\Loader\CallableLoader',
            $loader = new CallableLoader(
                $this->builder,
                new RouteCollection
            )
        );
    }

    /** @test */
    public function builderShouldBeNotified()
    {
        $called = false;
        $loader = new CallableLoader($this->builder, $routes = new RouteCollection);

        $this->builder->shouldReceive('addFileResource')
            ->with(__FILE__)
            ->andReturnUsing(function () use (&$called) {
                $called = true;
            });

        $loader->load(function ($r) use (&$routes) {
            $this->assertSame($r->getRoutes(), $routes);
        });

        $this->assertTrue($called);
    }

    /**
     * setUp
     *
     * @return void
     */
    protected function setUp()
    {
        $this->builder = $this->mockBuilder();
    }

    /**
     * tearDown
     *
     * @return void
     */
    protected function tearDown()
    {
        m::close();
    }
}
