<?php

/**
 * This File is part of the Selene\Components\DI\Tests package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\DI\Tests;

use \Mockery as m;
//use \AspectMock\Test as test;
use \Selene\Components\TestSuite\TestCase;
use \Selene\Components\DI\Container;
use \Selene\Components\DI\Reference;
use \Selene\Components\DI\ContainerInterface;
use \Selene\Components\DI\Tests\Stubs\FooService;
use \Selene\Components\DI\Tests\Stubs\BarService;
use \Selene\Components\DI\Tests\Stubs\ServiceFactory;
use \Selene\Components\DI\Tests\Stubs\SetterAwareService;
use \Selene\Components\DI\Tests\Stubs\LockedContainerStub;

/**
 * @class ContainerTest extends TestCase ContainerTest
 * @see TestCase
 *
 * @package Selene\Components\DI\Tests
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class ContainerTest extends TestCase
{
    /**
     * @var ClassName
     */
    protected $object;

    protected function setUp()
    {
        $this->container = new Container;
    }

    /**
     * @test
     */
    public function testContainerSetParams()
    {
        //$p = test::double('Selene\Components\DI\Parameters', ['set' => null, 'get' => 'foo']);

        $this->container->setParam('foo.service.class', 'foo');
        //$p->verifyInvoked('set');
        $this->assertSame('foo', $this->container->getParam('foo.service.class'));
        //$p->verifyInvoked('get');

        //$p = test::double('Selene\Components\DI\Parameters', ['set' => null, 'get' => ['opt1', 'opt2']]);

        $this->container->setParam('foo.options', $opts = ['opt1', 'opt2']);
        //$p->verifyInvoked('set');
        $this->assertSame($opts, $this->container->getParam('foo.options'));
        //$p->verifyInvoked('get');
    }

    /**
     * @test
     */
    public function testContainerScopeContainer()
    {
        $this->container->setParam('foo.service.class', 'StdClass');

        $this->container->setService('foo', '%foo.service.class%');

        $instanceA = $this->container->getService('foo');
        $instanceB = $this->container->getService('foo');

        $this->assertSame($instanceA, $instanceB);
    }

    /**
     * @test
     */
    public function testContainerScopePrototype()
    {
        $this->container->setParam('foo.service.class', 'StdClass');

        $this->container
            ->setService('foo', '%foo.service.class%')
            ->setScope(ContainerInterface::SCOPE_PROTOTYPE);

        $instanceA = $this->container->getService('foo');
        $instanceB = $this->container->getService('foo');

        $this->assertTrue($instanceA !== $instanceB);
    }

    /**
     * @test
     */
    public function testCreateServiceDefaultArgs()
    {
        $this->container->setParam('foo.options', $opts = ['opt1', 'opt2']);
        $this->container->setParam('foo.class', $fooclass = __NAMESPACE__.'\Stubs\FooService');

        $this->container->setService('foo_service', '%foo.class%', ['%foo.options%']);

        try {
            $fooClass = $this->container->getService('foo_service');
        } catch (\Exception $e) {
            $this->fail(sprintf('%s | [line]: %d | [FILE]: %s', $e->getMessage(), $e->getLine(), $e->getFile()));
        }

        $this->assertInstanceOf($fooclass, $fooClass);
        $this->assertSame($opts, $fooClass->getOptions());
    }

    /**
     * @test
     */
    public function testAddSetters()
    {
        $classname = __NAMESPACE__.'\Stubs\SetterAwareService';

        $this->container->setParam('setter.class', $classname);
        $this->container->setParam('setter.name', 'foo');
        $this->container->setService('my_service', '%setter.class%')->addSetter('setName', ['%setter.name%']);

        $service = $this->container->getService('my_service');

        $this->assertSame('foo', $service->name);
    }

    /**
     * @test
     */
    public function testAddSettersResolveArgs()
    {
        $classname = __NAMESPACE__.'\Stubs\SetterAwareService';

        $this->container->setParam('foo.class', $fooclass = __NAMESPACE__.'\Stubs\FooService');
        $this->container->setParam('setter.class', $classname);
        $this->container->setService('foo_service', '%foo.class%');

        $this->container->setService('my_service', '%setter.class%')
            ->addSetter('setFoo', [new Reference('foo_service')]);

        $fooService = $this->container->getService('foo_service');
        $service = $this->container->getService('my_service');

        $this->assertInstanceOf($fooclass, $service->foo);
        $this->assertSame($fooService, $service->foo);
    }

    /**
     * @test
     */
    public function testInjectServiceInstance()
    {
        $service = m::mock('Acme\InjectedService');
        $this->container->injectService('injected_service', $service);
        $this->assertSame($service, $this->container->getService('injected_service'));
    }

    /**
     * @test
     */
    public function testMergeContainers()
    {
        $container = new Container(null, 'merge.container');
        $container->setParam('foo', 'foo');
        $container->setService('foo', 'FooClass');

        $this->container->setParam('bar', 'bar');
        $this->container->setService('bar', 'BarClass');

        $this->container->merge($container);

        $this->assertTrue($this->container->hasService('foo'));
        $this->assertTrue($this->container->hasService('bar'));

        $this->assertEquals('foo', $this->container->getParam('foo'));
        $this->assertEquals('bar', $this->container->getParam('bar'));
    }

    /**
     * @test
     * @expectedException Selene\Components\DI\Exception\ContainerLockedException
     */
    public function testMergeContainerSouldRaiseExceptionWhenContainerToBeMergedIsLocked()
    {
        $this->container->merge(new LockedContainerStub());
    }

    /**
     * @test
     * @expectedException Selene\Components\DI\Exception\ContainerLockedException
     */
    public function testMergeContainerSouldRaiseExceptionWhenMergingContainerIsLocked()
    {
        $container = new LockedContainerStub();
        $container->merge($this->container);
    }

    /**
     * @test
     * @expectedException \LogicException
     */
    public function testMergeContainerSouldRaiseExceptionWhenMergingContainerWithTheSameName()
    {
        $container = new Container;
        $container->merge($this->container);
    }

    /**
     * @test
     */
    public function testContainerShouldBeServiceable()
    {
        $container = new Container();
        $this->assertSame($this->container, $this->container->getService(ContainerInterface::APP_CONTAINER_SERVICE));

        $container = new Container(null, 'foo.container');
        $this->assertSame($container, $container->getService('foo.container'));
    }

    /**
     * @test
     */
    public function testResolveAliasedService()
    {
        //$a = test::double('Selene\Components\DI\Aliases', ['add' => null, 'get' => 'my.service']);
        $this->container->injectService('my.service', $service = new \StdClass);

        $this->container->alias('my.service', 'alias.service');
        //$a->verifyInvoked('add');
        $s = $this->container->getService('alias.service');
        //$a->verifyInvoked('get');
        $this->assertSame($service, $s);
    }

    /**
     * @test
     */
    public function testConstructClassWithFactoryDependOnOtherService()
    {
        $this->container->setParam('foo.options', $opts = ['opt1', 'opt2']);
        $this->container->setParam('foo.class', __NAMESPACE__.'\Stubs\FooService');

        $this->container->setService('foo_service', '%foo.class%', ['%foo.options%']);

        $this->container->setService('bar_service', null)
            ->addArgument('$foo_service')
            ->setFactory(__NAMESPACE__.'\Stubs\ServiceFactory', 'makeBar');

        $barService = $this->container->getService('bar_service');

        $this->assertInstanceOf(__NAMESPACE__.'\Stubs\BarService', $barService);
        $this->assertInstanceOf(__NAMESPACE__.'\Stubs\FooService', $barService->getFoo());
    }

    /**
     * @test
     */
    //public function testParameterInheritance()
    //{
    //    $abstract = __NAMESPACE__.'\Stubs\AbstractService';

    //    $this->container->setParam('foo.service.class', $fooclass = __NAMESPACE__.'\Stubs\FooService');
    //    $this->container->setParam('inh.service.class', __NAMESPACE__.'\Stubs\InheritedService');
    //    $this->container->setParam($abstract, ['$foo_service']);
    //    $this->container->setService('foo_service', '%foo.service.class%');
    //    $this->container->setService('inh_service', '%inh.service.class%');

    //    $service = $this->container->getService('inh_service');
    //    $this->assertInstanceOf($fooclass, $service->foo);
    //}

    /**
     * @test
     */
    public function testConstructClassWithFactorySetArgs()
    {
        $this->container->setParam('foo.options', $opts = ['opt1', 'opt2']);

        $this->container->setService('foo_service', null, ['%foo.options%'])
            ->setFactory(__NAMESPACE__.'\Stubs\ServiceFactory', 'makeFoo');

        $fooService = $this->container->getService('foo_service');

        $this->assertInstanceOf(__NAMESPACE__.'\Stubs\FooService', $fooService, 'factory.with.args');
        $this->assertSame($opts, $fooService->getOptions(), 'factory.with.args');
    }

    /**
     * @test
     */
    public function testConstructClassWithFactoryAddArgs()
    {
        $this->container->setParam('foo.options', $opts = ['opt1', 'opt2']);
        $this->container->setService('foo_service', null)
            ->addArgument('%foo.options%')
            ->setFactory(__NAMESPACE__.'\Stubs\ServiceFactory', 'makeFoo');

        $fooService = $this->container->getService('foo_service');

        $this->assertInstanceOf(__NAMESPACE__.'\Stubs\FooService', $fooService, 'factory.add.args');
        $this->assertSame($opts, $fooService->getOptions(), 'factory.add.args');
    }
}
