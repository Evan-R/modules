<?php

/**
 * This File is part of the Selene\Components\DI\Tests\Dumper\Traits package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\DI\Tests\Dumper\Traits;

class FormatterTraitTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldFormatIntents()
    {
        $formatter = new FormatterStub;

        $this->assertSame('    ', $formatter->Indent(4));
        $this->assertSame('      ', $formatter->Indent(6));
        $this->assertSame('', $formatter->Indent(0));
    }

    /** @test */
    public function itShouldFormatVariables()
    {
        $formatter = new FormatterStub;

        $var = ['foo' => 'bar'];

        $str = $formatter->extractParams($var, 4);

        $this->assertEquals("[\n    'foo' => 'bar',\n]", $str);

        $var = ['foo' => null];

        $str = $formatter->extractParams($var, 4);

        $this->assertEquals("[\n    'foo' => null,\n]", $str);

        $var = ['foo' => true];

        $str = $formatter->extractParams($var, 4);

        $this->assertEquals("[\n    'foo' => true,\n]", $str);

        $var = ['foo' => ['bar' => false]];

        $str = $formatter->extractParams($var, 4);

        $this->assertEquals("[\n    'foo' => [\n        'bar' => false,\n    ],\n]", $str);

        $var = ['foo' => '$this->doStuff()'];

        $str = $formatter->extractParams($var, 4);

        $this->assertEquals("[\n    'foo' => \$this->doStuff(),\n]", $str);
    }
}
