<?php

/**
 * This File is part of the Selene\Module\DI\Tests\Dumper\Traits package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\DI\Tests\Dumper\Traits;

use Selene\Module\DI\Dumper\Traits\FormatterTrait;

class FormatterTraitTest extends \PHPUnit_Framework_TestCase
{
    use FormatterTrait;

    /** @test */
    public function itShouldFormatIntents()
    {
        $formatter = new FormatterStub;

        $this->assertSame('    ', $this->indent(4));
        $this->assertSame('      ', $this->indent(6));
        $this->assertSame('', $this->indent(0));
    }

    /** @test */
    public function itShouldFormatVariables()
    {

        $var = ['foo' => 'bar'];

        $str = $this->extractParams($var, 0);

        $this->assertEquals("[\n    'foo' => 'bar',\n]", $str);

        $str = $this->extractParams($var, 4);

        $this->assertEquals("    [\n        'foo' => 'bar',\n    ]", $str);

        $var = ['foo' => null];

        $str = $this->extractParams($var, 0);

        $this->assertEquals("[\n    'foo' => null,\n]", $str);

        $var = ['foo' => true];

        $str = $this->extractParams($var, 0);

        $this->assertEquals("[\n    'foo' => true,\n]", $str);

        $var = ['foo' => ['bar' => false]];

        $str = $this->extractParams($var, 0);

        $this->assertEquals("[\n    'foo' => [\n        'bar' => false,\n    ],\n]", $str);

        $var = ['foo' => '$this->doStuff()'];

        $str = $this->extractParams($var, 0);

        $this->assertEquals("[\n    'foo' => \$this->doStuff(),\n]", $str);

        $var = ['foo' => 'bar', 'baz' => ['foof' => 'swosh']];

        //$str = $this->extractParams($var, 0);
        //echo $this->extractParams($var, 0);
        //echo "\n";
        //echo $this->extractParams($var, 4);
        //echo "\n";
        //echo $this->extractParams($var, 8);
    }
}
