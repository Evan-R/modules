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

use \Selene\Components\DI\Reference;
use \Selene\Components\DI\CallerReference;

/**
 * @class CallerReferenceTest
 * @package Selene\Components\DI\Tests\Definition
 * @version $Id$
 */
class CallerReferenceTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof(
            'Selene\Components\DI\CallerReference',
            new CallerReference('foo', 'getBar')
        );
    }

    /** @test */
    public function itShouldCreateAreferenceIfStringGiven()
    {
        $def = new CallerReference('foo', 'getBar');
        $this->assertInstanceof('Selene\Components\DI\Reference', $def->getService());
    }

    /** @test */
    public function itShouldTakeAReferenceAsArgument()
    {
        $def = new CallerReference($foo = new Reference('foo'), 'getBar');
        $this->assertSame($foo, $def->getService());
    }

    /** @test */
    public function itShouldGetItsMethod()
    {
        $def = new CallerReference('foo', 'getBar');
        $this->assertSame('getBar', $def->getMethod());

        $def->setMethod('getFoo');
        $this->assertSame('getFoo', $def->getMethod());
    }

    /** @test */
    public function itShouldGetItsArguments()
    {
        $def = new CallerReference('foo', 'getBar', [1, 2]);
        $this->assertSame([1, 2], $def->getArguments());

        $def = new CallerReference('foo', 'getBar');
        $this->assertSame([], $def->getArguments());

        $def->setArguments($args = ['foo', 'bar']);
        $this->assertSame($args, $def->getArguments());

        $def->replaceArgument('baz', 1);
        $this->assertSame(['foo', 'baz'], $def->getArguments());
    }

    /** @test */
    public function itShouldThrowAnExceptionIfReplacingNonexistingArgument()
    {
        $def = new CallerReference('foo', 'getBar', [1, 2, 3]);

        try {
            $def->replaceArgument(4, 3);
        } catch (\OutOfBoundsException $e) {
            $this->assertTrue(true);
        }

        try {
            $def->replaceArgument('a', -1);
        } catch (\OutOfBoundsException $e) {
            $this->assertTrue(true);
        }
    }

    /** @test */
    public function itShouldCreateReferenceFromString()
    {
        $caller = CallerReference::fromString('$foo->getBar($baz, $bam)');

        $this->assertEquals(['$baz', '$bam'], $caller->getArguments());
        $this->assertEquals('$foo', $caller->getService());
        $this->assertEquals('getBar', $caller->getMethod());

        $caller = CallerReference::fromString('$foo->getBar');

        $this->assertEquals([], $caller->getArguments());
        $this->assertEquals('getBar', $caller->getMethod());

        $caller = CallerReference::fromString('$foo->getBar()');

        $this->assertEquals([], $caller->getArguments());
        $this->assertEquals('getBar', $caller->getMethod());
    }
}
