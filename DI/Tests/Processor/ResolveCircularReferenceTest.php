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

use \Selene\Components\DI\Container;
use \Selene\Components\DI\Reference;
use \Selene\Components\DI\Processor\ResolveCircularReference;

class ResolveCircularReferenceTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof('\Selene\Components\DI\Processor\ProcessInterface', new ResolveCircularReference);
    }

    /** @test */
    public function itShouldResolveRefenceOnDefinitionArguments()
    {
        $container = new Container;

        $container->define('foo', 'FooClass')->addArgument(new Reference('foo'));

        $process = new ResolveCircularReference;

        try {
            $process->process($container);
        } catch (\Selene\Components\DI\Exception\CircularReferenceException $e) {
            $this->assertSame('Service \'foo\' has circular reference on \'foo\'', $e->getMessage());
            return;
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
        $this->fail('test splipped');
    }

    /** @test */
    public function unrelatedArgsShouldPass()
    {
        $container = new Container;

        $container->define('bar', 'BarClass');
        $container->define('foo', 'FooClass')->addArgument(new Reference('bar'));

        $this->assertNull((new ResolveCircularReference)->process($container));

        $container = new Container;
        $container->define('foo', 'FooClass')->addArgument(1, 2, 3);

        $this->assertNull((new ResolveCircularReference)->process($container));

        $container = new Container;
        $container->define('foo', 'FooClass')->addArgument([1, 2, 3], [4, 5, 6]);

        $this->assertNull((new ResolveCircularReference)->process($container));
    }

    /** @test */
    public function selfReferencingOnSettersShouldNotBeOk()
    {
        $container = new Container;

        $container->define('foo', 'FooClass')->addSetter('setSelf', [new Reference('foo')]);

        try {
            (new ResolveCircularReference)->process($container);
        } catch (\Selene\Components\DI\Exception\CircularReferenceException $e) {
            $this->assertSame('Service \'foo\' has circular reference on \'foo\'', $e->getMessage());
            return;
        }

        $this->fail('test slipped');
    }

    /** @test */
    public function itShouldDetecteNestedReferences()
    {

        $container = new Container;

        $container->define('foo', 'FooClass')->addArgument(new Reference('bar'));
        $container->define('bar', 'BarClass')->addSetter('setFoo', [new Reference('foo')]);

        $process = new ResolveCircularReference;

        try {
            $process->process($container);
        } catch (\Selene\Components\DI\Exception\CircularReferenceException $e) {
            $this->assertSame('Service \'foo\' has circular reference on \'foo\'', $e->getMessage());
            return;
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }

        $this->fail('test splipped');
    }

    /** @test */
    public function itShouldDetecteReferencesOnSetterArguments()
    {

        $container = new Container;

        $container->define('foo', 'FooClass')->addSetter('setBar', [new Reference('bar')]);
        $container->define('bar', 'BarClass')->addSetter('setFoo', [new Reference('foo')]);

        $process = new ResolveCircularReference;

        try {
            $process->process($container);
        } catch (\Selene\Components\DI\Exception\CircularReferenceException $e) {
            $this->assertSame('Service \'foo\' has circular reference on \'foo\'', $e->getMessage());
            return;
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }

        $this->fail('test splipped');
    }

    /** @test */
    public function itShouldDetecteReferencesOnNestedSetterArguments()
    {
        $container = new Container;

        $container->define('test');

        $container->define('foo', 'FooClass')->addArgument(new Reference('bar'));
        $container->define('bar', 'BarClass')->addSetter(
            'setFoo',
            ['a' => ['foo' => new Reference('foo')], 'b' => [], new Reference('test')]
        );

        $process = new ResolveCircularReference;

        try {
            $process->process($container);
        } catch (\Selene\Components\DI\Exception\CircularReferenceException $e) {
            $this->assertSame('Service \'foo\' has circular reference on \'foo\'', $e->getMessage());
            return;
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }

        $this->fail('test splipped');
    }
}
