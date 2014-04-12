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
use \Selene\Components\Routing\Router;
use \Selene\Components\Routing\RouteCollectionInterface;

/**
 * @class RouterTest
 * @package Selene\Components\Routing\Tests
 * @version $Id$
 */
class RouterTest extends \PHPUnit_Framework_TestCase
{

    protected function subject()
    {
        $class = $this->getSubljectClass();
        $ref = new \ReflectionClass($class);
        if ($ref->getConstructor()) {
            return $ref->newInstanceArgs($this->getArgRequirements());
        }

        return $ref->newInstace();
    }

    protected function makeSubject($subjectClass)
    {
        if ($args = $this->getArgRequirements()) {
            $instance = new \ReflectionClass($subjectClass);
            return $instance->newInstanceArgs($args);
        }
    }

    protected function beConstructedWith()
    {

    }

    protected function getSubljectClass()
    {
        return '\Selene\Components\Routing\Router';
    }

    protected function let(RouteCollectionInterface $routes, RouteMatcherInterface $matcher)
    {
        $args = func_get_args();
        $this->subjectConstructorArgs = $args;
        var_dump($args);
    }


    protected function getArgRequirements()
    {
        $selfReflect = new \ReflectionClass($this);

        $cArgs = [];

        if ($selfReflect->hasMethod('let')) {
            $args = $selfReflect->getMethod('let')->getParameters();
            foreach ($args as $arg) {
                var_dump($arg->getClass());
                $cArgs[] = m::mock($arg->getClass());
            }
        }

        return $cArgs;
    }

    /**
     * @test
     */
    public function itSouldBeInstatiable()
    {
        $routes  = m::mock('Selene\Components\Routing\RouteCollectionInterface');
        $matcher = m::mock('Selene\Components\Routing\RouteMatcherInterface');

        $router = new Router($routes, $matcher);
        $this->assertInstanceOf('Selene\Components\Routing\Router', $router);
    }
}
