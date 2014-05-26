<?php

/**
 * This File is part of the Selene\Components\DI\Tests\Processor package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\DI\Tests\Processor;

use \Mockery as m;
use \Selene\Components\DI\Container;
use \Selene\Components\DI\Definition\ServiceDefinition as Definition;
use \Selene\Components\DI\Processor\ResolveDefinitionDependencies;

/**
 * @class ResolveDefinitionDependenciesTest
 * @package Selene\Components\DI\Tests\Processor
 * @version $Id$
 */
class ResolveDefinitionDependenciesTest extends \PHPUnit_Framework_TestCase
{

    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof('\Selene\Components\DI\Processor\ProcessInterface', new ResolveDefinitionDependencies);
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

        $this->assertSame('stdClass', $def->getClass());
        $this->assertSame(__FILE__, $def->getFile());
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
            $this->assertSame('class "Foo\FooClass" does not exist', $e->getMessage());
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
            $this->assertSame('file "'.$file.'" does not exist', $e->getMessage());
            return;
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }

        $this->fail('test splipped');
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
        $container = m::mock('Selene\Components\DI\ContainerInterface');
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
        return m::mock('Selene\Components\DI\ParameterInterface');
    }

    protected function tearDown()
    {
        m::close();
    }
}
