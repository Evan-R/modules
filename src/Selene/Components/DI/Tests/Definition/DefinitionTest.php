<?php

/**
 * This File is part of the Selene\Components\DI\Tests\Definition package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */
namespace Selene\Components\DI\Tests\Definition;

use \Mockery as m;
use \Selene\Components\DI\ContainerInterface;

/**
 * @abstract class DefinitionTest extends \PHPUnit_Framework_TestCase
 * @see \PHPUnit_Framework_TestCase
 * @abstract
 *
 * @package Selene\Components\DI\Tests\Definition
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
abstract class DefinitionTest extends \PHPUnit_Framework_TestCase
{
    protected $defaultScope;

    protected function setUp()
    {
        $this->defaultScope = ContainerInterface::SCOPE_CONTAINER;
    }

    protected function tearDown()
    {
        m::close();
    }

    /**
     * @test
     */
    public function testReplaceArgument()
    {
        m::mock($className = 'Foo\\Bar\\ClassDefinitionTestClassMock');
        $def = $this->createDefinition($className, [], $this->defaultScope);

        try {
            $def->replaceArgument('foo', 0);
        } catch (\OutOfBoundsException $e) {
            $this->assertEquals($e->getMessage(), 'Cannot replace argument at index 0, index is out of bounds');
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }

        $def = $this->createDefinition($className, ['foo', 'bar'], $this->defaultScope);
        $def->replaceArgument('bar', 0);
        $this->assertEquals(['bar', 'bar'], $def->getArguments());
        $def->replaceArgument('foo', 1);
        $this->assertEquals(['bar', 'foo'], $def->getArguments());
    }

    /**
     * @test
     */
    public function testRequiresFile()
    {
        $def = $this->createDefinition('foo');

        $this->assertFalse($def->requiresFile());

        $def->setFile('somefile.php');

        $this->assertTrue($def->requiresFile());
    }

    /** @test */
    public function itShouldGetArgumentAtIndex()
    {
        $def = $this->createDefinition('foo');
        $def->setArguments([1, 2, 3]);

        $this->assertSame(2, $def->getArgument(1));
    }

    /** @test */
    public function itShouldThrowIfIndexIsOutOfBounds()
    {
        $def = $this->createDefinition('foo');
        $def->setArguments([1, 2, 3]);

        try {
            $this->assertSame(2, $def->getArgument(3));
        } catch (\OutOfBoundsException $e) {
            $this->assertSame('Cannot get argument at index 3, index is out of bounds', $e->getMessage());
            return;
        }

        $this->fail('test slipped');
    }

    /**
     * @test
     */
    public function testHasParent()
    {
        $def = $this->createDefinition('bar');

        $this->assertFalse($def->hasParent());

        $def->setParent('foo');

        $this->assertTrue($def->hasParent());
        $this->assertSame('foo', $def->getParent());
    }

    /**
     * @test
     */
    public function testGetClass()
    {
        $def = $this->createDefinition('bar');
        $this->assertSame('bar', $def->getClass());
    }

    /**
     * @test
     */
    public function testGetArguments()
    {
        $def = $this->createDefinition('bar', $attr = ['foo', 'bar']);
        $this->assertSame($attr, $def->getArguments());

        $def = $this->createDefinition('bar');
        $this->assertSame([], $def->getArguments());
    }

    /** @test */
    public function testGetSetters()
    {

        m::mock($className = 'Foo\\Bar\\ClassDefinitionTestClassMock');

        $def = $this->createDefinition('bar');

        $this->assertFalse($def->hasSetters());
        $this->assertSame([], $def->getSetters());

        $def = $this->createDefinition($className);
        $def->addSetter('setFoo', ['foo']);
        $def->addSetter('setBar', ['bar']);

        $this->assertTrue($def->hasSetters());
        $this->assertEquals([['setFoo' =>  ['foo']], ['setBar' => ['bar']]], $def->getSetters());

    }

    /** @test */
    public function itShouldHaveSetters()
    {
        $def = $this->createDefinition('foo');
        $def->addSetter('setBar', []);

        $this->assertTrue($def->hasSetter('setBar'));
        $this->assertFalse($def->hasSetter('setFoo'));
    }

    /** @test */
    public function itShouldBeTaggable()
    {
        $def = $this->createDefinition('bar');

        $def->setMetaData('foo');

        $this->assertTrue($def->hasMetaData('foo'));
        $data = $def->getMetaData('foo');
        $this->assertInstanceof('\Selene\Components\DI\Meta\MetaDataInterface', $data);

        $def->setMetaData('foo', []);

        $this->assertInstanceof('\Selene\Components\DI\Meta\MetaDataInterface', $def->getMetaData('foo'));
        $this->assertFalse($data === $def->getMetaData('foo'));
    }

    /** @test */
    public function itShouldBeMergable()
    {
        $defA = $this->createDefinition('a');
        $defB = $this->createDefinition('b');

        $defB->setClass('Foo');

        $defA->merge($defB);
        $this->assertSame('Foo', $defA->getClass());

        $defA = $this->createDefinition('a');
        $defB = $this->createDefinition('b');

        $defA->setMetaData('foo', ['a']);
        $defB->setMetaData('bar', ['b']);

        $defA->merge($defB);
        $this->assertTrue($defA->hasMetaData('foo'));
        $this->assertTrue($defA->hasMetaData('bar'));

        $defA = $this->createDefinition('a');
        $defB = $this->createDefinition('b');

        $defA->setArguments(['a', 'b', 'c', 'd']);
        $defB->setArguments([1, 2, 3]);

        $defA->merge($defB);

        $this->assertSame([1, 2, 3, 'd'], $defA->getArguments());

        $defA = $this->createDefinition('a');
        $defB = $this->createDefinition('b');

        $defA->setArguments([1, 2, 3]);
        $defB->setArguments($args = ['a', 'b', 'c', 'd']);

        $defA->merge($defB);

        $this->assertSame($args, $defA->getArguments());
    }

    /** @test
     *  @expectedException \InvalidArgumentException
     */
    public function itShouldThrowExceptionWhenSettingAFactoryClosure()
    {
        $def = $this->createDefinition('test');
        $def->setFactory(function () {
        });
    }

    abstract protected function createDefinition($class = null, $arguments = [], $scope = null);
}
