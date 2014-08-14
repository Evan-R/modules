<?php

/**
 * This File is part of the Selene\Module\Routing\Tests package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Routing\Tests;

use \Mockery as m;
use \Selene\Module\Routing\Route;
use \Selene\Module\Routing\RouteCollection;
use \Selene\Module\Routing\StaticRouteCollection;
use \Selene\Module\Routing\RouteCollectionInterface;

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
        $c = m::mock('\Selene\Module\Routing\RouteCollectionInterface');
        $c->shouldReceive('raw')->andReturn([]);

        $collection = new StaticRouteCollection($c);

        try {
            $collection->add(m::mock('\Selene\Module\Routing\Route'));
        } catch (\BadMethodCallException $e) {
            $this->assertEquals('cannot add route to a static collection', $e->getMessage());
            return;
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }

        $this->fail('you loose');
    }
}
