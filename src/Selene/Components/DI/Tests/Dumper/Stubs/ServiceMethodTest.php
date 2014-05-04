<?php

/**
 * This File is part of the Selene\Components\DI\Tests\Dumper\Stubs package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\DI\Tests\Dumper\Stubs;

use \Mockery as m;
use \Selene\Components\DI\Reference;
use \Selene\Components\DI\Container;
use \Selene\Components\DI\ContainerInterface;
use \Selene\Components\DI\Dumper\Stubs\ServiceMethod;

/**
 * @class ServiceMethodTest
 * @package Selene\Components\DI\Tests\Dumper\Stubs
 * @version $Id$
 */
class ServiceMethodTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $svm = new ServiceMethod(m::mock('\Selene\Components\DI\ContainerInterface'), 'foo');
        $this->assertInstanceof('\Selene\Components\DI\Dumper\Stubs\StubInterface', $svm);
    }

    /** @test */
    public function itPrintAServiceMethod()
    {

        $container = new Container;
        $container->define('foo', 'Foo');

        $method = new ServiceMethod($container, 'foo');

        $svm = new ServiceMethod($container, 'foo');
        $this->assertStringEqualsFile(__DIR__.'/../Fixures/servicemethod.0', $method->dump());
    }

    /** @test */
    public function itShouldPrintDependencies()
    {
        $container = new Container;
        $container->define('bar', 'Bar');
        $container->define('foo', 'Foo', [new Reference('bar')]);

        $method = new ServiceMethod($container, 'foo');

        $svm = new ServiceMethod($container, 'foo');
        $this->assertStringEqualsFile(__DIR__.'/../Fixures/servicemethod.1', $method->dump());
    }

    /** @test */
    public function itShouldPrintCorrectReturnStatement()
    {
        $container = new Container;
        $container->define('foo', 'Foo', [], ContainerInterface::SCOPE_PROTOTYPE);

        $method = new ServiceMethod($container, 'foo');

        $svm = new ServiceMethod($container, 'foo');
        $this->assertStringEqualsFile(__DIR__.'/../Fixures/servicemethod.2', $method->dump());
    }
}
