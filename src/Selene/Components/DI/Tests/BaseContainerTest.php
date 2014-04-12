<?php

/**
 * This File is part of the Selene\Components\DI package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\DI\Tests;

use \Mockery as m;
use \Selene\Components\DI\BaseContainer;
use \Selene\Components\DI\ContainerInterface;
use \Selene\Components\DI\Definition;
use \Selene\Components\DI\DefinitionInterface;
use \Selene\Components\DI\Definition\ServiceDefinition;
use \Selene\Components\DI\Tests\Stubs\FooService;
use \Selene\Components\DI\Tests\Stubs\BarService;
use \Selene\Components\DI\Tests\Stubs\ChildService;
use \Selene\Components\DI\Tests\Stubs\ParentService;

class BaseContainerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function testGetInstance()
    {
        //$foo = __NAMESPACE__.'\\Stubs\FooService';
        //$class = m::mock($cn = '\BaseContainerTestMockClass');
        //$container = $this->createContainer();

        //$container->setClass($cn);

        //$this->assertInstanceOf(
        //    $cn,
        //    $container->getInstance($cn),
        //    sprintf('->getInstance() should be return object that is an instance of %s.', $cn)
        //);

        //$this->assertFalse(
        //    $container->getInstance($cn) === $container->getInstance($cn),
        //    '->getInstance() should not return the same instance.'
        //);

        //$container->setClass($cn)->setScope(ContainerInterface::SCOPE_CONTAINER);

        //$this->assertSame(
        //    $container->getInstance($cn),
        //    $container->getInstance($cn),
        //    '->getInstance() should return the same instance.'
        //);


        //$container = $this->createContainer();

        //$container->setClass($bar = __NAMESPACE__.'\\Stubs\\BarService');
        //$instance = $container->getInstance($bar);

        //$this->assertInstanceOf(
        //    $foo,
        //    $instance->getFoo(),
        //    sprintf('->getInstance() should set required arguments %s.', $foo)
        //);

        //$this->assertSame(
        //    $args = [],
        //    $instance->getFoo()->getOptions(),
        //    sprintf('->getInstance() should set required arguments %s.', var_export($args, true))
        //);

        //$container = $this->createContainer();
        //$container->setClass($bar);
        //$container->setClass($foo, [$args = ['a' => 'b']]);

        //$instance = $container->getInstance($bar);

        //$this->assertSame(
        //    $args,
        //    $instance->getFoo()->getOptions(),
        //    sprintf('->getInstance() should set required arguments %s.', var_export($args, true))
        //);

        //$instance = $container->getInstance($bar, [$arg = new FooService]);

        //$this->assertSame(
        //    $arg,
        //    $instance->getFoo(),
        //    sprintf('->getInstance() should set required arguments %s.', var_export($arg, true))
        //);
    }

    /**
     * @test
     */
    public function itShouldBeInstatiable()
    {
        $container = $this->createContainer();
        $this->assertInstanceOf('Selene\Components\DI\ContainerInterface', $container);
    }

    /**
     * @test
     */
    public function servicesShouldBeDefinableAndRecognized()
    {
        $container = $this->createContainer();

        $container->define('foo.service');
        $this->assertTrue($container->hasDefinition('foo.service'));
    }

    /**
     * @test
     */
    public function definitinSetterShouldRegisterRecognizableServices()
    {
        $container = $this->createContainer();

        $def = m::mock('\Selene\Components\DI\Definition\ServiceDefinition');

        $container->setDefinition('foo.service', $def);
        $this->assertTrue($container->hasDefinition('foo.service'));
    }

    /**
     * @test
     */
    public function injectingAServiceWithWrongScopeShouldThrowAnException()
    {
        $container = $this->createContainer();

        $service = m::mock('Container\Tests\ServiceStub');

        try {
            $container->inject('foo.service', $service, ContainerInterface::SCOPE_PROTOTYPE);
        } catch (\InvalidArgumentException $e) {
            $this->assertEquals($e->getMessage(), 'An injected service must not have a prototype scope');
            return;
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
        $this->fail('->inject() injecting a service wirth prototype scope should throw an exception');
    }

    /**
     * @test
     */
    public function serviceGettersShouldBeRetreivingAInjectedService()
    {
        $service = m::mock('Container\Tests\ServiceStub');
        $container = $this->createContainer();
        $container->inject('foo.service', $service);

        $this->assertEquals($service, $container->get('foo.service'));
    }

    /**
     * @test
     */
    public function serviceGettersShouldBeRetreiveDefinedServices()
    {
        $container = $this->createContainer();

        $class = __NAMESPACE__.'\\Stubs\\FooService';

        $margs = $this->getDefaultMockArgs($class, [$args = ['a' => 'b']]);
        $definition = $this->getDefinitionMock($margs);

        $container->setDefinition('foo.service', $definition);

        $this->assertInstanceOf($class, $foo = $container->get('foo.service'));

        try {
            $this->assertInstanceOf($class, $bar = $container->get('foo.service'));
        } catch (\Mockery\Exception\InvalidCountException $e) {
            // definition is not touched again.
            $this->assertTrue(true);
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }

        $this->assertEquals($args, $foo->getOptions());
        $this->assertSame($foo, $bar);

        $class = __NAMESPACE__.'\\Stubs\\BarService';

        $reference = m::mock('Selene\Components\DI\Reference');
        $reference->shouldReceive('__toString')->andReturn('foo.service');


        $args = $this->getDefaultMockArgs($class, [$reference]);
        $definition = $this->getDefinitionMock($args);

        $container->setDefinition('bar.service', $definition);
        $this->assertInstanceOf($class, $bar = $container->get('bar.service'));
        $this->assertSame($foo, $bar->getFoo());
    }

    /**
     * @test
     */
    public function testSetterInjection()
    {
        $container = $this->createContainer();

        $class = $fooClass = __NAMESPACE__.'\\Stubs\\FooService';

        $margs = $this->getDefaultMockArgs($class);
        $definition = $this->getDefinitionMock($margs);

        $container->setDefinition('foo.service', $definition);

        $class = __NAMESPACE__.'\\Stubs\\SetterAwareService';

        $reference = $this->createReferenceMock('foo.service');

        $args = $this->getDefaultMockArgs($class, [], [
            ['hasSetters', 'once', true],
            ['getSetters', 'once', [['setName' => ['stub']], ['setFoo' => [$reference]]]]
        ]);

        $definition = $this->getDefinitionMock($args);

        $container->setDefinition('stub.service', $definition);

        $this->assertInstanceOf($class, $stub = $container->get('stub.service'));
        $this->assertSame('stub', $stub->name);

        $this->assertInstanceOf(
            $class,
            $container->get('stub.service'),
            '->get() should return an instance of ' .$class
        );

        $this->assertInstanceOf(
            $fooClass,
            $fooInstance = $stub->foo,
            '->get() should auto setter inject an instance of '.$fooClass.' into '.$class
        );

        $this->assertSame(
            $fooInstance,
            $container->get('foo.service'),
            '->get() once a container scoped service is resolved the container should always return the same instance'
        );
    }

    /**
     * @test
     */
    public function testSetAlias()
    {
        $container = $this->createContainer();
        $service = m::mock('Container\Tests\ServiceStub');
        $container->inject('foo.service', $service);
        $container->setAlias('foo', $this->createAliasMock('foo.service'));
        $container->setAlias('bar', 'foo.service');

        $this->assertSame($service, $container->get('foo.service'));
        $this->assertSame($service, $container->get('foo'));
        $this->assertSame($service, $container->get('bar'));
    }

    /**
     * @test
     */
    public function testDefinitionInheritance()
    {
        $container = $this->createContainer();

        $container->define('parent.service', $parentClass = __NAMESPACE__.'\\Stubs\\ParentService', ['foo'])
            ->addSetter('setBar', ['bar']);
        $container->define('child.service', $childClass = __NAMESPACE__.'\\Stubs\\ChildService')
            ->setParent('parent.service');

        $this->assertInstanceOf($childClass, $child = $container->get('child.service'));
        $this->assertEquals('foo', $child->getFoo());
        $this->assertEquals('bar', $child->getBar());
    }

    /**
     * @test
     */
    public function testDeclareAbstract()
    {
        $container = $this->createContainer();
        $def = m::mock('Selene\Components\DI\Definition\DefinitionInterface');
        $def->shouldReceive('isInternal')->andReturn(false);
        $def->shouldReceive('isAbstract')->andReturn(true);

        $container->setDefinition($service = 'abstract.service', $def);

        try {
            $container->get($service);
        } catch (\Selene\Components\DI\Exception\ContainerResolveException $e) {
            $this->assertEquals(
                $e->getMessage(),
                sprintf('Service %s is declared abstract. Instantiating abstract services is not allowed.', $service),
                '->get() should throw an excetion on abstract services'
            );
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    /**
     * @test
     */
    public function testDeclareInternal()
    {
        $container = $this->createContainer();
        $def = m::mock('Selene\Components\DI\Definition\DefinitionInterface');
        $def->shouldReceive('isInternal')->andReturn(true);

        $container->setDefinition($service = 'internal.service', $def);

        try {
            $container->get($service);
        } catch (\Selene\Components\DI\Exception\ContainerResolveException $e) {
            $this->assertEquals(
                $e->getMessage(),
                sprintf('A service with id %s was is not defined', $service),
                '->get() should throw an excetion on abstract services'
            );
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
    }


    protected function getDefaultMockArgs($class = null, array $arguments = [], array $custom = [])
    {
        $args = [
            ['getClass', 'once', $class],
            ['setClass', 'once', null],
            ['isInternal', 'once', false],
            ['isAbstract', 'once', false],
            ['hasArguments', null, true],
            ['getArguments', 'once', $arguments],
            ['hasSetters', null, false],
            ['requiresFile', null, false],
            ['hasParent', null, false],
            ['hasFactory', null, false],
            ['scopeIsContainer', null, true],
        ];

        if (!empty($custom)) {
            $filter = [];
            return array_filter(array_merge($custom, $args), function ($def) use (&$filter) {
                list($method, $times, $value) = $def;
                if (!in_array($method, $filter)) {
                    $filter[] = $method;
                    return true;
                }
            });
        }
        return $args;
    }

    /**
     * tearDown
     *
     * @access protected
     * @return mixed
     */
    protected function tearDown()
    {
        m::close();
    }

    /**
     * createContainer
     *
     * @param DefinitionInterface $definition
     * @param string $name
     *
     * @access protected
     * @return BaseContainer Container instance
     */
    protected function createContainer(ParameterInterface $params = null, $name = 'basecontainer')
    {
        return new BaseContainer($params, $name);
    }

    /**
     * getDefinitionMock
     *
     * @param array $arguments
     * @param mixed $class
     *
     * @access protected
     * @return Object
     */
    protected function getDefinitionMock(array $arguments = null, $class = null)
    {
        $class = $class ?: 'Selene\Components\DI\Definition\ServiceDefinition';

        $def = m::mock($class);
        foreach ($arguments as $argument) {

            if ($arguments[1] === 'once') {
                $def->shouldReceive($argument[0])->once()->andReturn($argument[2]);
            } elseif (is_int($argument[1])) {
                $def->shouldReceive($argument[0])->times($argument[1])->andReturn($argument[2]);
            } else {
                $def->shouldReceive($argument[0])->andReturn($argument[2]);
            }
        }
        return $def;
    }

    protected function createReferenceMock($id)
    {
        $reference = m::mock('Selene\Components\DI\Reference');
        $reference->shouldReceive('__toString')->andReturn($id);
        return $reference;
    }

    protected function createAliasMock($id)
    {
        $alias = m::mock('Selene\Components\DI\Alias');
        $alias->shouldReceive('__toString')->andReturn($id);
        return $alias;
    }
}
