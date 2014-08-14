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

use \Selene\Module\DI\Container;
use \Selene\Module\DI\Reference;
use \Selene\Module\DI\CallerReference;
use \Selene\Module\DI\Processor\ResolveCircularReference;

class ResolveCircularReferenceTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof('\Selene\Module\DI\Processor\ProcessInterface', new ResolveCircularReference);
    }

    /** @test */
    public function itShouldResolveRefenceOnDefinitionArguments()
    {
        $container = new Container;

        $container->define('foo', 'FooClass')->addArgument(new Reference('foo'));

        $process = new ResolveCircularReference;

        try {
            $process->process($container);
        } catch (\Selene\Module\DI\Exception\CircularReferenceException $e) {
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
        } catch (\Selene\Module\DI\Exception\CircularReferenceException $e) {
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
        } catch (\Selene\Module\DI\Exception\CircularReferenceException $e) {
            $this->assertSame('Service \'foo\' has circular reference on \'bar\'', $e->getMessage());
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
        } catch (\Selene\Module\DI\Exception\CircularReferenceException $e) {
            $this->assertSame('Service \'foo\' has circular reference on \'bar\'', $e->getMessage());
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
        } catch (\Selene\Module\DI\Exception\CircularReferenceException $e) {
            $this->assertSame('Service \'foo\' has circular reference on \'bar\'', $e->getMessage());
            return;
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }

        $this->fail('test splipped');
    }

    /** @test */
    public function itShouldDetectCircularReferencesOnCallers()
    {
        $container = new Container;

        $container->define('bar');

        $container->define('foo')
            ->addArgument(new CallerReference('bar', 'getStuff'))
            ->addArgument(new CallerReference('foo', 'getBar'));

        $process = new ResolveCircularReference;

        try {
            $process->process($container);
        } catch (\Selene\Module\DI\Exception\CircularReferenceException $e) {
            $this->assertSame('Service \'foo\' has circular reference on \'foo\'', $e->getMessage());
            return;
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }

        $this->fail('test splipped');
    }

    /** @test */
    public function itShouldDetectNestedReferences()
    {
        $container = new Container;

        // A needs D
        $A = $container->define('a');

        // A needs D
        $B = $container->define('b');

        // C needs D
        $C= $container->define('c');

        // D needs C
        $D = $container->define('d');

        $container->setAlias('_d', 'd');
        $container->setAlias('_c', 'c');

        $A->addArgument(new Reference('_d'));
        $B->addArgument(new Reference('_d'));
        $C->addArgument(new Reference('_d'));
        $D->addArgument(new Reference('_c'));

        $process = new ResolveCircularReference;

        try {
            $process->process($container);
        } catch (\Selene\Module\DI\Exception\CircularReferenceException $e) {
            $this->assertSame('Service \'d\' has circular reference on \'c\'', $e->getMessage());
            return;
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }

        $this->fail('test splipped');
    }
}
