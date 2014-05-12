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

abstract class DefinitionTest extends \PHPUnit_Framework_TestCase
{

    protected $defaultScope;

    protected function setUpt()
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

    /**
     * @test
     */
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
    public function itShouldBeFlagable()
    {
        $def = $this->createDefinition('bar');

        $def->addFlag('foo');

        $this->assertTrue($def->hasFlag('foo'));
        $this->assertInstanceof('\Selene\Components\DI\Definition\FlagInterface', $flag = $def->getFlag('foo'));
    }

    abstract protected function createDefinition($class = null, $arguments = [], $scope = null);
}
