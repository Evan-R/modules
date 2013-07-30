<?php

/**
 * This File is part of the Selene\Components\DependencyInjection\Tests package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\DependencyInjection\Tests;

use Mockery as m;
use Selene\Components\TestSuite\TestCase;
use Selene\Components\DependencyInjection\Container;
use Selene\Components\DependencyInjection\Tests\Stubs\ExtendedContainer;

class ContainerTest extends TestCase
{
    /**
     * @var ClassName
     */
    protected $container;

    protected function setUp()
    {
        $this->container = new Container;

        parent::setUp();
    }

    /**
     * @test
     */
    public function testIsShared()
    {
        $this->container->bind('foo', function () {
            return 'bar';
        }, true);

        $this->assertTrue($this->container->isShared('foo'));
    }

    /**
     * @test
     */
    public function testSetInstance()
    {
        $this->container->instance('foo', new \StdClass);
        $this->assertInstanceOf('\StdClass', $this->container['foo']);
    }

    /**
     * @test
     */
    public function testSetAlias()
    {
        $this->container->bind('foo', function () {return 'bar';});
        $this->container->alias('foo', 'bar');

        $this->assertEquals($this->container['foo'], $this->container['bar']);
    }

    /**
     * @test
     */
    public function testSetAliasDefinition()
    {
        $this->container->bind(['foo' => 'bar'], function () {return 'bar';});

        $this->assertEquals($this->container['foo'], $this->container['bar']);
    }

    /**
     * @test
     */
    public function testSetAliasInstance()
    {
        $this->container->instance('foo', new \StdClass);
        $this->container->alias('foo', 'bar');

        $this->assertSame($this->container['foo'], $this->container['bar']);
    }

    /**
     * @test
     */
    public function testSetAliasInstanceDefinition()
    {
        $this->container->instance(['foo' => 'bar'], new \StdClass);

        $this->assertSame($this->container['foo'], $this->container['bar']);
    }

    /**
     * @test
     */
    public function testExtend()
    {
        $this->container->bind('foo', function ($container) {
            return 'Thomas';
        });

        $this->container->extend('foo', function ($instance) {
            return 'Hello ' . $instance;
        });

        $this->assertEquals('Hello Thomas', $this->container['foo']);
    }

    /**
     * @test
     */
    public function testSetterInjection()
    {
        $this->container
            ->bind('bar', __NAMESPACE__.'\\SetterAwareStub')
            ->call('setFoo', 'baz');

        $this->assertEquals('baz', $this->container['bar']->foo);
    }

    /**
     * @test
     */
    public function testSetterInjectionResolveClosure()
    {
        $this->container['baz'] = function () {
            return new \StdClass;
        };

        $this->container
            ->bind('bar', __NAMESPACE__.'\\SetterAwareStub')
            ->call('setFoo', 'baz');

        $this->assertInstanceOf('\StdClass', $this->container['bar']->foo);
    }

    /**
     * testSetterInjectionResolveArguments
     *
     * @access public
     * @return mixed
     */
    public function testSetterInjectionResolveArguments()
    {
        $this->container
            ->bind('bar', __NAMESPACE__.'\\SetterAwareStub')
            ->call('setStub');

        $this->assertInstanceOf(__NAMESPACE__.'\\DependentStub', $this->container['bar']->stub);
    }

    /**
     * testAddArgument
     *
     * @access public
     * @return mixed
     */
    public function testAddArgument()
    {
        $this->container->singleton('bar', __NAMESPACE__.'\\ResolveableStub');

        $this->container
            ->bind('foo', __NAMESPACE__.'\\DependentStub')
            ->addArgument('bar');

        $instance = $this->container->resolve('foo');
        $this->assertSame($this->container->resolve('bar'), $instance->stub);

    }

    /**
     * @test
     */
    public function testResolveClosure()
    {
        $this->container->bind('foo', function () {
            return 'bar';
        });

        $this->assertSame('bar', $this->container->resolve('foo'));
    }

    /**
     * @test
     */
    public function testBindSinleton()
    {
        $this->container->singleton('foo', $class = __NAMESPACE__.'\\ResolveableStub');

        $instanceA = $this->container->resolve('foo');
        $instanceB = $this->container->resolve('foo');

        $this->assertInstanceOf($class, $instanceA);
        $this->assertSame($instanceA, $instanceB);
    }

    /**
     * @test
     */
    public function testContainerShare()
    {
        $object = new \StdClass;

        $this->container['foo'] = $this->container->share(function ()
        {
            return new \StdClass;
        });

        $instanceA = $this->container['foo'];
        $instanceB = $this->container['foo'];

        $this->assertInstanceOf('\StdClass', $instanceA);
        $this->assertSame($instanceA, $instanceB);
    }


    /**
     * @test
     */
    public function testArrayAccessSet()
    {
        $this->container['foo'] = 'bar';
        $this->assertTrue($this->container->isBound('foo'));
    }

    /**
     * @test
     */
    public function testArrayAccessGet()
    {
        $this->container['foo'] = 'bar';
        $this->assertSame('bar', $this->container['foo']);
        $this->assertSame('bar', $this->container->resolve('foo'));
    }

    /**
     * @test
     */
    public function testResolveDependencies()
    {
        $this->container->bind('foo', __NAMESPACE__.'\\DependentStub');
        $instance = $this->container['foo'];

        $this->assertInstanceOf(__NAMESPACE__.'\\ResolveableStub', $instance->stub);
    }

    /**
     * @test
     */
    public function testGetterResolver()
    {
        $container = new ExtendedContainer;

        $instance = $container['foo_bar'];
        $this->assertInstanceOf(__NAMESPACE__.'\\Stubs\Foo', $instance);

        $instance = $container['foo.bar-baz'];
        $this->assertInstanceOf(__NAMESPACE__.'\\Stubs\Foo', $instance);
    }

    /**
     * @test
     * @expectedException Selene\Components\DependencyInjection\Exception\ContainerBindException
     */
    public function testResolveUninstantiableShouldThrowException()
    {
        $this->container->resolve(__NAMESPACE__.'\\UnresolvableStub');
    }

    /**
     * @test
     * @expectedException Selene\Components\DependencyInjection\Exception\ContainerBindException
     */
    public function testBindToAnInterfaceShouldThrowException()
    {
        $this->container->bind(__NAMESPACE__.'\\ResolveableStubInterface', __NAMESPACE__.'\\ResolveableStub');
    }

    /**
     * @test
     * @expectedException Selene\Components\DependencyInjection\Exception\ContainerBindException
     */
    public function testBindToNoneExistingClassShouldThrowException()
    {
        $this->container->bind('foo');
    }

    /**
     * @test
     * @expectedException Selene\Components\DependencyInjection\Exception\ContainerBindException
     */
    public function testClassToClassShouldThrowAnException()
    {
        $this->container->bind(__NAMESPACE__.'\\DependentStub', __NAMESPACE__.'\\ResolveableStub');
    }

    /**
     * @test
     * @expectedException \ReflectionException
     */
    public function testResolveNoneExistingClassShouldThrowReflectionExteption()
    {
        $this->container['IdoNotExist'];
    }

    /**
     * @test
     * @expectedException Selene\Components\DependencyInjection\Exception\ContainerBindException
     */
    public function testOverwriteGetterSouldThrowException()
    {
        $container = new ExtendedContainer;
        $instance = $container['foo_bar'] = 'bar';
    }
}

class DependentStub
{
    public $stub;
    public function __construct(ResolveableStub $stub)
    {
        $this->stub = $stub;
    }
}

interface ResolveableStubInterface
{}

class ResolveableStub implements ResolveableStubInterface
{}

class SetterAwareStub
{
    public $foo;
    public $stub;
    public function setFoo($foo)
    {
        $this->foo = $foo;
    }
    public function setStub(DependentStub $stub)
    {
        $this->stub = $stub;
    }

}

class UnresolvableStub
{
    private function __construct()
    {
    }
}
