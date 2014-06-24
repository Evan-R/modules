<?php

/**
 * This File is part of the Selene\Components\Routing\Tests package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Routing\Tests;

use \Mockery as m;
use \Selene\Components\Routing\Route;
use \Selene\Components\Routing\RouteCollection;
use \Selene\Components\Routing\StaticRouteCollection;
use \Selene\Components\Routing\RouteCollectionInterface;

class StaticRouteCollectionTest extends \PHPUnit_Framework_TestCase
{
    protected function tearDown()
    {
        m::close();
    }

    /**
     * @test
     */
    public function itShouldThrowAnExceptionWhenAttemptingToAddARoute()
    {
        $c = m::mock('\Selene\Components\Routing\RouteCollectionInterface');
        $c->shouldReceive('raw')->andReturn([]);

        $collection = new StaticRouteCollection($c);

        try {
            $collection->add(m::mock('\Selene\Components\Routing\Route'));
        } catch (\BadMethodCallException $e) {
            $this->assertEquals('cannot add route to a static collection', $e->getMessage());
            return;
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }

        $this->fail('you loose');
    }
}
