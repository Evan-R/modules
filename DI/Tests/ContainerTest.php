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
use \Selene\Components\DI\Container;
use \Selene\Components\DI\ContainerInterface;
use \Selene\Components\DI\Reference;
use \Selene\Components\DI\Parameters;
use \Selene\Components\DI\DefinitionInterface;
use \Selene\Components\DI\Definition\ServiceDefinition;
use \Selene\Components\DI\Tests\Stubs\FooService;
use \Selene\Components\DI\Tests\Stubs\BarService;
use \Selene\Components\DI\Tests\Stubs\ChildService;
use \Selene\Components\DI\Tests\Stubs\SetterAwareService;
use \Selene\Components\DI\Tests\Stubs\ParentService;
use \Selene\Components\DI\Tests\Stubs\ContainerStub;
use \Selene\Components\DI\Tests\Stubs\ServiceFactory;
use \Selene\Components\DI\Exception\ContainerResolveException;
use \Selene\Components\DI\Exception\ContainerLockedException;

use \Selene\Components\TestSuite\TestCase;

/**
 * @class ContainerTest extends TestCase
 * @see TestCase
 *
 * @package Selene\Components\DI\Tests
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
class ContainerTest extends TestCase
{

    /** @test */
    public function itShouldBeInstatiable()
    {
        $container = $this->createContainer();
        $this->assertInstanceOf('Selene\Components\DI\ContainerInterface', $container);
    }

    /** @test */
    public function itShouldSetAndRetrieveParameters()
    {
        $container = $this->createContainer();

        $this->assertFalse($container->hasParameter('foo'));

        $container->setParameter('foo', 'bar');
        $this->assertTrue($container->hasParameter('foo'));

        $this->assertSame('bar', $container->getParameter('foo'));

        $this->assertInstanceof('Selene\Components\DI\ParameterInterface', $container->getParameters());

        $parameters = m::mock('Selene\Components\DI\ParameterInterface');

        $container->replaceParameters($parameters);

        $this->assertSame($parameters, $container->getParameters());
    }

    /** @test */
    public function itShouldThrowExceptionOnParamReplacementIfContainerIsLocked()
    {
        $container = new Container(m::mock('Selene\Components\DI\StaticParameters'));

        try {
            $container->replaceParameters(m::mock('Selene\Components\DI\ParameterInterface'));
        } catch (ContainerLockedException $e) {
            $this->assertSame('Can\'t replace parameters on a locked container.', $e->getMessage());
            return;
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }

        $this->giveUp();
    }

    /** @test */
    public function servicesShouldBeDefinableAndRecognized()
    {
        $container = $this->createContainer();

        $container->define('foo.service');
        $this->assertTrue($container->hasDefinition('foo.service'));
    }

    /** @test */
    public function definitionsShouldBeRetreivable()
    {
        $container = $this->createContainer();

        foreach (['foo', 'bar', 'baz'] as $name) {
            $container->setDefinition($name, m::mock('\Selene\Components\DI\Definition\DefinitionInterface'));
        }

        $this->assertSame(3, count($container->getDefinitions()));
    }

    /** @test */
    public function definitionsShouldBeRemoveable()
    {
        $container = $this->createContainer();

        $container->define('foo', 'stdClass');
        $this->assertTrue($container->hasDefinition('foo'));

        $container->removeDefinition('foo');
        $this->assertFalse($container->hasDefinition('foo'));
    }

    /** @test */
    public function itShouldFindDefinitionsWithMetaData()
    {
        $defs = [];
        $container = $this->createContainer();

        foreach (['foo', 'bar', 'baz'] as $i => $name) {
            $defs[$name] = m::mock('\Selene\Components\DI\Definition\DefinitionInterface');
            $container->setDefinition($name, $defs[$name]);
        }

        $defs['foo']->shouldReceive('hasMetaData')->andReturn(true);
        $defs['bar']->shouldReceive('hasMetaData')->andReturn(false);
        $defs['baz']->shouldReceive('hasMetaData')->andReturn(true);

        $meta = $container->findDefinitionsWithMetaData();

        $this->assertSame(2, count($meta));

        $defs = [];
        $container = $this->createContainer();

        foreach (['foo', 'bar', 'baz'] as $i => $name) {
            $defs[$name] = m::mock('\Selene\Components\DI\Definition\DefinitionInterface');
            $container->setDefinition($name, $defs[$name]);
        }

        $defs['foo']->shouldReceive('hasMetaData')->with('meta')->andReturn(false);
        $defs['bar']->shouldReceive('hasMetaData')->with('meta')->andReturn(true);
        $defs['baz']->shouldReceive('hasMetaData')->with('meta')->andReturn(false);

        $meta = $container->findDefinitionsWithMetaData('meta');

        $this->assertSame($defs['bar'], $meta['bar']);
    }

    /** @test */
    public function itShouldReturnResolvedServices()
    {
        $container = $this->createContainer();

        $this->assertSame([], $container->getServices());

        $container->inject('foo', $service = new \StdClass);

        $this->assertSame(['foo' => $service], $container->getServices());
    }

    /** @test */
    public function definitinSetterShouldRegisterRecognizableServices()
    {
        $container = $this->createContainer();

        $def = m::mock('\Selene\Components\DI\Definition\ServiceDefinition');

        $container->setDefinition('foo.service', $def);
        $this->assertTrue($container->hasDefinition('foo.service'));
    }

    /** @test */
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

    /** @test */
    public function internalServicesShouldNotDirectlyRetrieveable()
    {
        $container = $this->createContainer();

        $args = $this->getDefaultMockArgs('FooServiceClass', [], [
            ['isInternal', null, true]
            ]);
        $def = $this->getDefinitionMock($args);

        $container->setDefinition('foo.service', $def);

        try {
            $container->get('foo.service');
        } catch (ContainerResolveException $e) {
            $this->assertSame('A service with id foo.service was is not defined', $e->getMessage());
            return;
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
            return;
        }

        $this->giveUp();
    }

    /** @test */
    public function itHasAService()
    {
        $container = new Container;

        $this->assertFalse($container->has('foo'));

        $container->inject('foo', m::mock('Foo'));

        $this->assertTrue($container->has('foo'));

        $container = new Container;

        $this->assertFalse($container->has('foo'));

        $container->define('foo', 'stdClass');

        $this->assertTrue($container->has('foo'));
    }

    /** @test */
    public function serviceGettersShouldBeRetreivingAInjectedService()
    {
        $service = m::mock('Container\Tests\ServiceStub');
        $container = $this->createContainer();
        $container->inject('foo.service', $service);

        $this->assertEquals($service, $container->get('foo.service'));
    }

    /** @test */
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

    /** @test */
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
            ['isInjected', 'once', false],
            ['hasSetters', 'once', true],
            ['getSetters', 'once', [[ 'setName' => ['stub']], ['setFoo' => [$reference]]]]
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

    /** @test */
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
     * @expectedException \LogicException
     */
    public function itShouldThrowExceptionIfAliasAndServiceNameExists()
    {
        $container = $this->createContainer();

        $container->define('foo');
        $container->setAlias('foo', 'bar');
    }

    /**
     * @test
     * @expectedException \LogicException
     */
    public function itShouldThrowExceptionIfAliasAndServiceNameMatch()
    {
        $container = $this->createContainer();

        $container->setAlias('foo', 'foo');
    }

    /** @test */
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

    /** @test */
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

    /** @test */
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

    /**
     * @test */
    public function itShouldThrowExceptionOnCircularReference()
    {
        $container = $this->createContainer();

        $container->define('foo', '\Selene\Components\DI\Tests\Stubs\BarService')->addArgument(new Reference('foo'));

        try {
            $container->get('foo');
        } catch (\Selene\Components\DI\Exception\CircularReferenceException $e) {
            $this->assertSame('service foo is in a circular reference', $e->getMessage());
            return;
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }

        $this->fail('test splipped');
    }

    /** @test */
    public function itShouldThrowInvalidArgumentExceptionIfDefinitionClassDoesNotExist()
    {
        $container = $this->createContainer();
        $container->define('service', 'SomeFakeClass');

        try {
            $container->get('service');
        } catch (\RuntimeException $e) {
            $this->assertSame('Class \SomeFakeClass does not exist', $e->getMessage());
            return;
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }

        $this->fail('test splipped');
    }

    /** @test */
    public function itShouldRequireFileOnDefinitions()
    {
        $container = $this->createContainer();
        $container->define('foo', $class = 'DI\Stubs\RequiredService')
            ->setFile(__DIR__.DIRECTORY_SEPARATOR.'Stubs'.DIRECTORY_SEPARATOR.'RequiredService.php');

        $this->assertFalse(class_exists($class));
        $container->get('foo');
        $this->assertTrue(class_exists($class));
    }

    /** @test */
    public function injectedServicesShouldSynchronizeSetters()
    {
        $container = $this->createContainer();

        $def = new ServiceDefinition('\Selene\Components\DI\Tests\Stubs\SetterAwareService');
        $def->addSetter('setFoo', ['$injected']);
        $injected = new ServiceDefinition('\Selene\Components\DI\Tests\Stubs\FooService');
        $injected->setInjected(true);

        $container->setDefinition('dependent', $def);
        $container->setDefinition('injected', $injected);

        $dependent = $container->get('dependent');

        $this->assertTrue(null === $dependent->foo);

        $container->inject('injected', $foo = new FooService);

        $this->assertSame($foo, $dependent->foo);


        // trigger by multiple injected

        $container = $this->createContainer();

        $def = new ServiceDefinition('\Selene\Components\DI\Tests\Stubs\SetterAwareService');
        $def->addSetter('setFooBar', ['$injected', '$injectedb']);
        $injected = new ServiceDefinition('\Selene\Components\DI\Tests\Stubs\FooService');
        $injected->setInjected(true);

        $injectedB = new ServiceDefinition('\StdClass');
        $injectedB->setInjected(true);

        $container->setDefinition('dependent', $def);
        $container->setDefinition('injected', $injected);
        $container->setDefinition('injectedb', $injectedB);

        $dependent = $container->get('dependent');

        $this->assertTrue(null === $dependent->foo);
        $this->assertTrue(null === $dependent->bar);

        $container->inject('injected', $foo = new FooService);

        $this->assertTrue(null === $dependent->foo);
        $this->assertTrue(null === $dependent->bar);

        $container->inject('injectedb', $bar = new \StdClass);

        $this->assertSame($foo, $dependent->foo);
        $this->assertSame($bar, $dependent->bar);
    }

    /** @test */
    public function itShouldResolveStaticConstructors()
    {
        $container = new ContainerStub;

        $this->assertInstanceof('stdClass', $service = $container->get('foo'));

        $this->assertSame($service, $container->get('foo'));
    }

    /** @test */
    public function itShouldThrowExceptionWhenLockedWhileSettingADefinition()
    {
        $def    = m::mock('Selene\Components\DI\Definition\DefinitionInterface');
        $params = m::mock('Selene\Components\DI\StaticParameters, Selene\Components\DI\ParameterInterface');

        $container = new Container($params);

        try {
            $container->setDefinition('foo', $def);
        } catch (\BadMethodCallException $e) {
            $this->assertSame('Cannot set definition "foo" on a locked container.', $e->getMessage());

            return;
        }

        $this->giveUp();
    }

    /** @test */
    public function aliasesShouldBeSettable()
    {
        $container = $this->createContainer();

        $container->define('foo', 'stdClass');

        $container->setAlias('bar', 'foo');

        $this->assertInstanceof('stdClass', $service = $container->get('bar'));

        $container->removeAlias('bar');

        try {
            $container->get('bar');
        } catch (\Selene\Components\DI\Exception\ContainerResolveException $e) {
            $this->assertSame('A service with id bar was is not defined', $e->getMessage());
        }
    }

    /** @test */
    public function containerShouldBeMergable()
    {
        $container = new Container;
        $container->setParameter('test', 'abc');

        $mock = m::mock('Selene\Components\DI\ContainerInterface');
        $mock->shouldReceive('isLocked')->once()->andReturn(false);
        $mock->shouldReceive('getServices')->once()->andReturn(['foo' => new \StdClass]);
        $mock->shouldReceive('getParameters')->once()->andReturn($params = new Parameters);
        $mock->shouldReceive('getDefinitions')->once()->andReturn([]);
        $mock->shouldReceive('getAliases')->once()->andReturn(['bar' => 'foo']);
        $params->set('testB', 'def');

        $container->merge($mock);

        $container->hasParameter('testB');
        $this->assertInstanceof('stdClass', $container->get('foo'));
    }

    /** @test */
    public function itShouldHaveAliases()
    {
        $this->assertInstanceof('Selene\Components\DI\Aliases', (new Container)->getAliases());
    }

    /** @test */
    public function itShouldBuildDefinitionsWithFactory()
    {
        $container = new Container;
        $container->setParameter('foo_args', $args = [1, 2, 4]);
        $container
            ->define('foo_service', $serviceClass = 'Selene\Components\DI\Tests\Stubs\FooService')
            ->setFactory('Selene\Components\DI\Tests\Stubs\ServiceFactory', 'makeFoo')
            ->addArgument('%foo_args%');

        $service = $container->get('foo_service');

        $this->assertInstanceof($serviceClass, $service);
        $this->assertSame($args, $service->getOptions());

        $container
            ->define('bar_service', $serviceClassB = 'Selene\Components\DI\Tests\Stubs\BarService')
            ->setFactory('Selene\Components\DI\Tests\Stubs\ServiceFactory', 'makeBar')
            ->addArgument(new Reference('foo_service'));

        $serviceB = $container->get('bar_service');

        $this->assertInstanceof($serviceClassB, $serviceB);
        $this->assertInstanceof($serviceClass, $serviceB->getFoo());
        $this->assertSame($service, $serviceB->getFoo());

        $container
            ->define('bar_service_b', $serviceClassB = 'Selene\Components\DI\Tests\Stubs\BarService')
            ->setFactory('Selene\Components\DI\Tests\Stubs\ServiceFactory', 'makeBarB');

        try {
            $container->get('bar_service_b');
        } catch (\InvalidArgumentException $e) {
            $this->assertSame('Factory is not callable', $e->getMessage());
            return;
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }

        $this->fail('test splipped');
    }

    /** @test */
    public function containerShouldNotBeMergableIfOneMemberIsLocked()
    {
        $container = new Container;

        $mock = m::mock('Selene\Components\DI\ContainerInterface');
        $mock->shouldReceive('isLocked')->andReturn(true);

        try {
            $container->merge($mock);
        } catch (ContainerLockedException $e) {
            $this->assertSame('Cannot merge a locked container.', $e->getMessage());
            return;
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }

        $mock = m::mock('Selene\Components\DI\ContainerInterface');
        $mock->shouldReceive('isLocked')->andReturn(false);
        $container = new Container(m::mock('Selene\Components\DI\StaticParameters'));
        try {
            $container->merge($mock);
        } catch (ContainerLockedException $e) {
            $this->assertSame('Cannot merge a locked container.', $e->getMessage());
            return;
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
            ['isInjected', 'once', false],
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
     * @return Container Container instance
     */
    protected function createContainer(ParameterInterface $params = null, $list = null, $name = 'Container')
    {
        return new Container($params, $list, $name);
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
