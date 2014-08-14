<?php

/**
 * This File is part of the Selene\Module\DI\Tests\Processor package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\DI\Tests\Processor;

use \Mockery as m;
use \Selene\Module\DI\Container;
use \Selene\Module\DI\Definition\ServiceDefinition as Definition;
use \Selene\Module\DI\Processor\ResolveDefinitionDependencies;

/**
 * @class ResolveDefinitionDependenciesTest
 * @package Selene\Module\DI\Tests\Processor
 * @version $Id$
 */
class ResolveDefinitionDependenciesTest extends \PHPUnit_Framework_TestCase
{

    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof('\Selene\Module\DI\Processor\ProcessInterface', new ResolveDefinitionDependencies);
    }

    /** @test */
    public function itShouldResolveClassAndFileParams()
    {
        $params = $this->getParametersMock();
        $params->shouldReceive('resolveParam')->with('%foo.class%')->andReturn('stdClass');
        $params->shouldReceive('resolveParam')->with('%foo.file%')->andReturn(__FILE__);

        $def = new Definition('%foo.class%');
        $def->setFile('%foo.file%');

        $definitions = [
            'foo' => $def
        ];

        $container = $this->getContainerMock($params, $definitions);

        $process = new ResolveDefinitionDependencies;

        $process->process($container);

        $this->assertSame('\stdClass', $def->getClass());
        $this->assertSame(__FILE__, $def->getFile());
    }

    /** @test */
    public function itShouldNotSetAFileIfNotRequired()
    {
        $params = $this->getParametersMock();
        $params->shouldReceive('resolveParam')->with('%foo.class%')->andReturn('stdClass');

        $def = new Definition('%foo.class%');

        $definitions = [
            'foo' => $def
        ];

        $container = $this->getContainerMock($params, $definitions);

        $process = new ResolveDefinitionDependencies;

        $process->process($container);

        $this->assertSame('\stdClass', $def->getClass());
        $this->assertFalse($def->requiresFile());
    }

    /** @test */
    public function itShouldThrowExceptionIfClassDoesNotExist()
    {

        $params = $this->getParametersMock();
        $params->shouldReceive('resolveParam')->with('%foo.class%')->andReturn('Foo\FooClass');

        $def = new Definition('%foo.class%');

        $definitions = [
            'foo' => $def
        ];

        $container = $this->getContainerMock($params, $definitions);

        $process = new ResolveDefinitionDependencies;

        try {
            $process->process($container);
        } catch (\InvalidArgumentException $e) {
            $this->assertSame('class "Foo\FooClass" required by service "foo" does not exist', $e->getMessage());
            return;
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }

        $this->fail('test splipped');
    }

    /** @test */
    public function itShouldThrowExceptionIfFileDoesNotExists()
    {
        $file = '/somedir/somefile.php';
        $params = $this->getParametersMock();
        $params->shouldReceive('resolveParam')->with('%foo.class%')->andReturn('stdClass');
        $params->shouldReceive('resolveParam')->with($file)->andReturn($file);

        $def = new Definition('%foo.class%');
        $def->setFile($file);

        $definitions = [
            'foo' => $def
        ];

        $container = $this->getContainerMock($params, $definitions);

        $process = new ResolveDefinitionDependencies;

        try {
            $process->process($container);
        } catch (\InvalidArgumentException $e) {
            $this->assertSame('file "'.$file.'" required by service "foo" does not exist', $e->getMessage());
            return;
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }

        $this->fail('test splipped');
    }

    /** @test */
    public function itShoultThrowExceptionIfFactoryClassDoesNotExists()
    {

        $container = new Container;

        $container
            ->define('foo_service', 'stdClass')
            ->setFactory('some_callback');


        $process = new ResolveDefinitionDependencies;

        try {
            $process->process($container);
        } catch (\InvalidArgumentException $e) {
            $this->assertEquals(
                'Service factory for service "foo_service" requires a valid callback',
                $e->getMessage()
            );
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }

        $container = new Container;
        $container
            ->define('bar_service', 'stdClass')
            ->setFactory('FooFactory::make');

        $process = new ResolveDefinitionDependencies;

        try {
            $process->process($container);
        } catch (\InvalidArgumentException $e) {
            $this->assertEquals(
                'class "FooFactory" required by service "bar_service" does not exist',
                $e->getMessage()
            );
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }


        $container = new Container;

        $container->setParameter('foo_factory.class', 'BarFactory');
        $container
            ->define('bar_service', 'stdClass')
            ->setFactory('%foo_factory.class%', 'makeFoo');

        $process = new ResolveDefinitionDependencies;

        try {
            $process->process($container);
        } catch (\InvalidArgumentException $e) {
            $this->assertEquals(
                'class "BarFactory" required by service "bar_service" does not exist',
                $e->getMessage()
            );
            return;
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }

        $this->fail('test splipped');
    }

    /** @test */
    public function itShouldBeOkWehnRequireingTheFile()
    {
        $file = __DIR__.DIRECTORY_SEPARATOR.'Fixures'.DIRECTORY_SEPARATOR.'class.0.php';

        $container = new Container;
        $container
            ->define('foo_service', 'stdClass')
            ->setFile($file)
            ->setFactory('FooFactory::make');

        $process = new ResolveDefinitionDependencies;

        $this->assertNull($process->process($container));
    }

    /** @test */
    public function itShouldThrowIfFactoryMethodDoesNotExists()
    {
        $file = __DIR__.DIRECTORY_SEPARATOR.'Fixures'.DIRECTORY_SEPARATOR.'class.0.php';

        $container = new Container;
        $container
            ->define('foo_service', 'stdClass')
            ->setFile($file)
            ->setFactory('FooFactory', 'makeFoo');

        $process = new ResolveDefinitionDependencies;

        try {
            $process->process($container);
        } catch (\InvalidArgumentException $e) {
            $this->assertEquals(
                'Service factory for service "foo_service" requires a valid callback',
                $e->getMessage()
            );
            return;
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }

        $this->fail('test splipped');
    }

    /** @test */
    public function itShouldBeOkIfFactoryIsValidCallback()
    {
        $file = __DIR__.DIRECTORY_SEPARATOR.'Fixures'.DIRECTORY_SEPARATOR.'function.0.php';
        $container = new Container;
        $container
            ->define('foo_service', 'stdClass')
            ->setFile($file)
            ->setFactory('DiTests\makeFoo');

        $process = new ResolveDefinitionDependencies;

        $this->assertNull($process->process($container));
    }

    /**
     * getContainerMock
     *
     * @param mixed $parameters
     * @param mixed $definitions
     *
     * @access protected
     * @return ContainerInterface
     */
    protected function getContainerMock($parameters, $definitions)
    {
        $container = m::mock('Selene\Module\DI\ContainerInterface');
        $container->shouldReceive('getParameters')->andReturn($parameters);
        $container->shouldReceive('getDefinitions')->andReturn($definitions);

        return $container;
    }

    /**
     * getParametersMock
     *
     * @access protected
     * @return ParameterInterface
     */
    protected function getParametersMock()
    {
        return m::mock('Selene\Module\DI\ParameterInterface');
    }

    protected function tearDown()
    {
        m::close();
    }
}
