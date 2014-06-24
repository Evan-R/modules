<?php

/**
 * This File is part of the Selene\Components\DI package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\DI\Tests\Dumper\Stubs;

use \Selene\Components\DI\Dumper\Stubs\Lines;

/**
 * @class LinesTest extends \PHPUnit_Framework_TestCase
 * @see \PHPUnit_Framework_TestCase
 *
 * @package Selene\Components\DI
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class LinesTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $lines = new Lines;
        $this->assertInstanceof('\Selene\Components\DI\Dumper\Stubs\StubInterface', $lines);
    }

    /** @test */
    public function itShouldIndentLines()
    {
        $lines = new Lines;
        $lines->indent();
        $lines->add('foo');
        $this->assertSame('    foo', $lines->dump());

        $lines = new Lines;
        $lines->indent();
        $lines->indent();
        $this->assertSame('        foo', (string)$lines->add('foo'));

        $lines = new Lines;
        $lines->indent();
        $lines->indent();
        $lines->end();
        $this->assertSame('    foo', (string)$lines->add('foo'));
    }

    /** @test */
    public function itShouldConcatLines()
    {
        $lines = new Lines;
        $lines
            ->add('foo')
            ->indent()
            ->add('bar')
            ->end()
            ->add('baz');

        $this->assertSame("foo\n    bar\nbaz", (string)$lines);
    }

    /** @test */
    public function itShouldAddNewLies()
    {

        $lines = new Lines;
        $lines
            ->add('foo')
            ->emptyLine()
        ->end();

        $this->assertSame("foo\n", (string)$lines);
    }

    /** @test */
    public function itShouldSetOuputIndent()
    {
        $lines = new Lines;
        $lines
            ->add('foo')
            ->indent()
            ->add('bar')
            ->end()
            ->add('baz');

        $lines->setOutputIndentation(4);

        $this->assertSame("    foo\n        bar\n    baz", (string)$lines);
    }
}
