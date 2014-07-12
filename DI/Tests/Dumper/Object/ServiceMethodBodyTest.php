<?php

/**
 * This File is part of the Selene\Components\DI\Tests\Dumper\Object package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\DI\Tests\Dumper\Object;

use \Selene\Components\DI\Container;
use \Selene\Components\DI\Reference;
use \Selene\Components\DI\Definition\CallerDefinition;
use \Selene\Components\DI\Dumper\Object\ServiceMethodBody;

/**
 * @class ServiceMethodBodyTets
 * @package Selene\Components\DI\Tests\Dumper\Object
 * @version $Id$
 */
class ServiceMethodBodyTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itIsExpectedThat()
    {
        $container = new Container;

        $container->define('foo', 'Acme\Foo');

        $smb = new ServiceMethodBody($container, 'foo');

        $expected = "return \$this->services['foo'] = new \Acme\Foo;";

        $this->assertSame($expected, $smb->generate());
    }

    /** @test */
    public function itIsExpectedThatAlias()
    {
        $container = new Container;

        $container->define('foo', 'Acme\Foo');

        $smb = new ServiceMethodBody($container, 'foo', 'FooAlias');

        $expected = "return \$this->services['foo'] = new FooAlias;";

        $this->assertSame($expected, $smb->generate());
    }

    /** @test */
    public function itShouldPrintArguments()
    {
        $container = new Container;

        $container->define('foo', 'Acme\Foo')
            ->addArgument('string')
            ->addArgument(new Reference('bar'));
        $container->define('bar', 'Acme\Bar');

        $smb = new ServiceMethodBody($container, 'foo', 'FooAlias');

        $expected = "return \$this->services['foo'] = new FooAlias(\n    'string',\n    \$this->get('bar')\n);";

        $this->assertSame($expected, $smb->generate());
    }

    /** @test */
    public function itShouldSetMethodCalls()
    {
        $container = new Container;

        $container->define('foo', 'Acme\Foo')
            ->addSetter('callCaller');
        $smb = new ServiceMethodBody($container, 'foo', 'FooAlias');

        $expected = file_get_contents(__DIR__.'/../Fixures/servicemethodbody.0');
        $gen = $smb->generate();

        $this->assertSame(trim($expected), trim($gen));

        $container->define('bar', 'Acme\Bar');
        $container->define('foo', 'Acme\Foo')
            ->addSetter('callCaller', [new Reference('bar')]);
        $smb = new ServiceMethodBody($container, 'foo', 'FooAlias');

        $expected = file_get_contents(__DIR__.'/../Fixures/servicemethodbody.1');
        $gen = $smb->generate();

        $this->assertSame(trim($expected), trim($gen));
    }

    /** @test */
    public function itShouldSetCallerDefinitions()
    {
        $container = new Container;

        $container->define('bar', 'Acme\Bar');

        $container->define('foo', 'Acme\Foo')
            ->addArgument(new CallerDefinition('bar', 'getStuff'));

        $smb = new ServiceMethodBody($container, 'foo', 'FooAlias');

        $expected = file_get_contents(__DIR__.'/../Fixures/servicemethodbody.2');
        $gen = $smb->generate();

        $this->assertSame(trim($expected), trim($gen));
    }

    /** @test */
    public function itShouldPrintNestedServiceArguments()
    {
        $container = new Container;

        $container->define('bar', 'Acme\Bar');

        $container->define('foo', 'Acme\Foo')
            ->addArgument([new Reference('bar')]);

        $smb = new ServiceMethodBody($container, 'foo', 'FooAlias');

        $expected = file_get_contents(__DIR__.'/../Fixures/servicemethodbody.3');
        $gen = $smb->generate();

        $this->assertSame(trim($expected), trim($gen));
    }

    /** @test */
    public function itShouldPringItsFactory()
    {
        $container = new Container;

        $container->define('foo', 'Acme\Foo')
            ->setFactory($factory = get_class($this), 'makeStaticFoo');

        $smb = new ServiceMethodBody($container, 'foo', 'FooAlias');

        $expected = 'return $this->services[\'foo\'] = \\'.$factory.'::makeStaticFoo();';
        $gen = $smb->generate();
        $this->assertSame(trim($expected), trim($gen));

        $container->define('foo', 'Acme\Foo')
            ->setFactory($factory = get_class($this), 'makeFoo');

        $expected = 'return $this->services[\'foo\'] = (new \\'.$factory.')->makeFoo();';
        $gen = $smb->generate();
        $this->assertSame(trim($expected), trim($gen));
    }

    /** @test */
    public function itShouldCreateSyncCallback()
    {

        $container = new Container;

        $container->define('bar', 'Acme\Bar')->setInjected(true);
        $container->define('foo', 'Acme\Foo')
            ->addSetter('setBar', [new Reference('bar')]);

        $smb = new ServiceMethodBody($container, 'foo', 'FooAlias');

        $expected = file_get_contents(__DIR__.'/../Fixures/servicemethodbody.4');
        $gen = $smb->generate();

        $this->assertSame(trim($expected), trim($gen));
    }

    /**
     * TestFactory
     */
    public function makeFoo()
    {
    }

    /**
     * TestFactory
     */
    public static function makeStaticFoo()
    {
    }
}
