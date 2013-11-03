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
use Selene\Components\DependencyInjection\Tests\Stubs\FooService;
use Selene\Components\DependencyInjection\Tests\Stubs\BarService;
use Selene\Components\DependencyInjection\Tests\Stubs\SetterAwareService;

/**
 * @class ContainerTest extends TestCase ContainerTest
 * @see TestCase
 *
 * @package Selene\Components\DependencyInjection\Tests
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
        $this->container->setParam('foo.service.class', 'foo');
        $this->assertSame('foo', $this->container->getParam('@foo.service.class'));

        $this->container->setParam('foo.options', $opts = ['opt1', 'opt2']);
        $this->assertSame($opts, $this->container->getParam('@foo.options'));
    }

    /**
     * @test
     */
    public function testContainerScopeContainer()
    {
        $this->container->setParam('foo.service.class', 'StdClass');

        $this->container->setService('foo', '@foo.service.class');

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
            ->setService('foo', '@foo.service.class')
            ->setScope(Container::SCOPE_PROTOTYPE);

        $instanceA = $this->container->getService('foo');
        $instanceB = $this->container->getService('foo');

        $this->assertTrue($instanceA !== $instanceB);
    }

    /**
     * @test
     */
    public function testAddSetters()
    {
        $classname = __NAMESPACE__.'\Stubs\SetterAwareService';

        $this->container->setParam('setter.class', $classname);
        $this->container->setParam('setter.name', 'foo');
        $this->container->setService('my_service', '@setter.class')->addSetter('setName', ['@setter.name']);

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
        $this->container->setService('foo_service', '@foo.class');

        $this->container->setService('my_service', '@setter.class')
            ->addSetter('setFoo', ['$foo_service']);

        $fooService = $this->container->getService('foo_service');
        $service = $this->container->getService('my_service');

        $this->assertInstanceOf($fooclass, $service->foo);
        $this->assertSame($fooService, $service->foo);
    }

    /**
     * @test
     */
    public function testParameterInheritance()
    {
        $abstract = __NAMESPACE__.'\Stubs\AbstractService';

        $this->container->setParam('foo.service.class', $fooclass = __NAMESPACE__.'\Stubs\FooService');
        $this->container->setParam('inh.service.class', __NAMESPACE__.'\Stubs\InheritedService');
        $this->container->setParam($abstract, ['$foo_service']);
        $this->container->setService('foo_service', '@foo.service.class');
        $this->container->setService('foo_service', '@foo.service.class');
        $this->container->setService('inh_service', '@inh.service.class');

        $service = $this->container->getService('inh_service');
        $this->assertInstanceOf($fooclass, $service->foo);
    }

    protected function tearDown()
    {

    }
}
